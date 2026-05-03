<?php
// app/Console/Commands/RepairWaitlistCommand.php

namespace App\Console\Commands;

use App\Models\Event;
use App\Services\WaitlistService;
use Illuminate\Console\Command;

class RepairWaitlistCommand extends Command
{
    protected $signature = 'waitlist:repair
                            {event_id? : ID de l\'événement à réparer (tous si omis)}
                            {--dry-run : Affiche ce qui serait fait sans rien modifier}';

    protected $description = 'Répare la liste d\'attente : promeut les premiers en liste si des places sont disponibles.';

    public function __construct(private WaitlistService $service)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $eventId = $this->argument('event_id');
        $dryRun  = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Mode dry-run activé — aucune modification ne sera effectuée.');
        }

        $query = Event::query()->where('places_disponibles', '>', 0);

        if ($eventId) {
            $query->where('id', $eventId);
        }

        $events = $query->get();

        if ($events->isEmpty()) {
            $this->info('Aucun événement avec des places disponibles et une liste d\'attente.');
            return Command::SUCCESS;
        }

        foreach ($events as $event) {
            $enAttente = $event->listeAttente()->count();

            if ($enAttente === 0) {
                $this->line("  Event #{$event->id} \"{$event->titre}\" — pas de liste d'attente, ignoré.");
                continue;
            }

            $this->line("Event #{$event->id} \"{$event->titre}\" — {$event->places_disponibles} place(s) dispo, {$enAttente} en attente");

            if ($dryRun) {
                $this->info("  → [dry-run] Promouvrait le premier de la liste.");
                continue;
            }

            $promus = 0;
            while ($event->places_disponibles > 0) {
                $promu = $this->service->promouvoirPremier($event);
                if (!$promu) break;

                $this->info("  ✓ User #{$promu->user_id} promu pour event #{$event->id}");
                $promus++;
                $event->refresh();
            }

            if ($promus === 0) {
                $this->warn("  Aucun utilisateur promu pour event #{$event->id}.");
            }
        }

        $this->info('');
        $this->info('Réparation terminée.');
        return Command::SUCCESS;
    }
}