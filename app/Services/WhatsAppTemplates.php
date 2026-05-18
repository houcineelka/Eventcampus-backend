<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Carbon;

class WhatsAppTemplates
{
    public static function rappelEvenement(User $user, Event $event): string
    {
        return "🎓 EventCampus — Rappel\n\n"
            . "Bonjour {$user->prenom},\n\n"
            . "Vous êtes inscrit à l'événement *{$event->titre}* demain.\n\n"
            . "📅 Date : " . Carbon::parse($event->date)->format('d/m/Y') . "\n"
            . "🕐 Heure : {$event->heure}\n"
            . "📍 Lieu : {$event->lieu}\n\n"
            . "À demain !";
    }
}
