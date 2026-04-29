<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendRappelEmails extends Command
{
    protected $signature = 'rappel:send';

    protected $description = 'Envoie les emails de rappel aux étudiants inscrits aux événements du lendemain';

    public function handle()
    {
        $this->info('Envoi des emails de rappel...');

        $tomorrow = Carbon::tomorrow()->toDateString();

        $events = Event::whereDate('date', $tomorrow)
            ->with('participants')
            ->get();

        if ($events->isEmpty()) {
            $this->info('Aucun événement demain.');
            return;
        }

        foreach ($events as $event) {
            $this->info("Événement : {$event->titre} — {$event->participants->count()} inscrit(s)");
        }

        $this->info('Terminé.');
    }
}
