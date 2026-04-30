<?php

namespace App\Notifications;

use App\Models\Club;
use App\Models\User;
use Illuminate\Notifications\Notification;

class NouvelleAdhesionNotification extends Notification
{
    public function __construct(
        private Club $club,
        private User $etudiant
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'club_id'    => $this->club->id,
            'club_nom'   => $this->club->nom,
            'etudiant_id'=> $this->etudiant->id,
            'etudiant'   => $this->etudiant->name,
            'message'    => "{$this->etudiant->name} a rejoint votre club \"{$this->club->nom}\".",
        ];
    }
}
