<?php

namespace App\Notifications;

use App\Models\Club;
use App\Models\Message;
use Illuminate\Notifications\Notification;

class NouveauMessageNotification extends Notification
{
    public function __construct(
        private Message $message,
        private Club $club
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'club_id'      => $this->club->id,
            'club_nom'     => $this->club->nom,
            'message_id'   => $this->message->id,
            'sender_id'    => $this->message->user_id,
            'sender_name'  => $this->message->sender->name,
            'content'      => $this->message->content,
            'message'      => "{$this->message->sender->name} a envoyé un message dans {$this->club->nom}.",
        ];
    }
}
