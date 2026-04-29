<?php

namespace App\Console\Commands;

use App\Mail\RappelEvenementMail;
use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

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

        $totalSent = 0;

        foreach ($events as $event) {
            foreach ($event->participants as $user) {
                Mail::to($user->email)->send(new RappelEvenementMail($user, $event));
                $this->info("Email envoyé à {$user->email} pour l'événement : {$event->titre}");
                $totalSent++;
            }
        }

        $this->info("Terminé. {$totalSent} email(s) envoyé(s).");
    }
}
