<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendRappelWhatsApp extends Command
{
    protected $signature = 'rappel:whatsapp';

    protected $description = 'Envoie les rappels WhatsApp aux étudiants inscrits aux événements du lendemain';

    public function handle(WhatsAppService $whatsapp)
    {
        $this->info('Envoi des rappels WhatsApp...');

        $tomorrow = Carbon::tomorrow()->toDateString();

        $events = Event::whereDate('date', $tomorrow)
            ->with(['participants', 'club'])
            ->get();

        if ($events->isEmpty()) {
            $this->info('Aucun événement demain.');
            return;
        }

        $totalSent = 0;

        foreach ($events as $event) {
            foreach ($event->participants as $user) {
                if (!$user->whatsapp_reminders || !$user->phone) {
                    continue;
                }

                $message = "🎓 EventCampus — Rappel\n\n"
                    . "Bonjour {$user->prenom},\n\n"
                    . "Vous êtes inscrit à l'événement *{$event->titre}* demain.\n\n"
                    . "📅 Date : " . Carbon::parse($event->date)->format('d/m/Y') . "\n"
                    . "🕐 Heure : {$event->heure}\n"
                    . "📍 Lieu : {$event->lieu}\n\n"
                    . "À demain !";

                try {
                    $whatsapp->send($user->phone, $message);
                    $this->info("WhatsApp envoyé à {$user->phone} pour : {$event->titre}");
                    $totalSent++;
                } catch (\Exception $e) {
                    $this->error("Erreur pour {$user->phone} : {$e->getMessage()}");
                }
            }
        }

        $this->info("Terminé. {$totalSent} message(s) WhatsApp envoyé(s).");
    }
}
