<?php

namespace App\Notifications;

use App\Models\Event;
use App\Models\User;
use Illuminate\Notifications\Notification;

class NouvelleInscriptionNotification extends Notification
{
    public function __construct(
        private Event $event,
        private User  $etudiant
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event_id'    => $this->event->id,
            'event_titre' => $this->event->titre,
            'etudiant_id' => $this->etudiant->id,
            'etudiant'    => $this->etudiant->name,
            'message'     => "{$this->etudiant->name} s'est inscrit à votre événement \"{$this->event->titre}\".",
        ];
    }
}
