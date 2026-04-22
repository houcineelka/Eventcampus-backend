<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoleRequestSubmitted extends Notification
{
    use Queueable;

    public function __construct(public \App\Models\RoleRequest $roleRequest)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $type = $this->roleRequest->is_existing_student
            ? 'Étudiant existant'
            : 'Nouvel utilisateur';

        return (new MailMessage)
            ->subject('Nouvelle demande de rôle organisateur')
            ->greeting('Bonjour Admin,')
            ->line('Une nouvelle demande de rôle organisateur a été soumise.')
            ->line('**Nom :** ' . $this->roleRequest->name)
            ->line('**Email :** ' . $this->roleRequest->email)
            ->line('**Type :** ' . $type)
            ->line('**Statut :** En attente')
            ->action('Voir les demandes', url('/admin/role-requests'))
            ->line('Merci de traiter cette demande dans les plus brefs délais.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'role_request_id' => $this->roleRequest->id,
            'name'            => $this->roleRequest->name,
            'email'           => $this->roleRequest->email,
        ];
    }
}
