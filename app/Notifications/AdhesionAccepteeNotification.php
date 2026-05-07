<?php

namespace App\Notifications;

use App\Models\Club;
use Illuminate\Notifications\Notification;

class AdhesionAccepteeNotification extends Notification
{
    public function __construct(
        private Club $club
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'club_id'  => $this->club->id,
            'club_nom' => $this->club->nom,
            'message'  => "Votre demande d'adhésion au club \"{$this->club->nom}\" a été acceptée.",
        ];
    }
}