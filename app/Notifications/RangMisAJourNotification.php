<?php
// app/Notifications/RangMisAJourNotification.php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class RangMisAJourNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Event $event,
        public readonly int   $nouveauRang,
    ) {}

    /**
     * In-app uniquement — pas d'email pour les simples mises à jour de rang
     * pour éviter de spammer l'utilisateur.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'         => 'rang_mis_a_jour',
            'event_id'     => $this->event->id,
            'event_titre'  => $this->event->titre,
            'nouveau_rang' => $this->nouveauRang,
            'message'      => "Votre rang est maintenant N°{$this->nouveauRang} pour \"{$this->event->titre}\".",
        ];
    }
}