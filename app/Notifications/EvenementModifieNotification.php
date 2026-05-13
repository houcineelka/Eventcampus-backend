<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Notifications\Notification;

class EvenementModifieNotification extends Notification
{
    public function __construct(
        private Event $event
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
            'message'     => "L'événement \"{$this->event->titre}\" a été modifié et est en attente d'approbation.",
        ];
    }
}
