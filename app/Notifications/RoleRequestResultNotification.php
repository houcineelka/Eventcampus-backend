<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class RoleRequestResultNotification extends Notification
{
    use Queueable;

    public function __construct(private string $status) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->status === 'approved') {
            return (new MailMessage)
                ->subject('Votre demande de rôle organisateur a été acceptée')
                ->greeting('Bonjour ' . $notifiable->name . ',')
                ->line('Félicitations ! Votre demande de rôle organisateur a été approuvée.')
                ->line('Vous pouvez maintenant créer et gérer des clubs et des événements.')
                ->action('Accéder à la plateforme', url('/dashboard'))
                ->line('Bienvenue dans l\'équipe des organisateurs !');
        }

        return (new MailMessage)
            ->subject('Votre demande de rôle organisateur a été refusée')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Après examen, votre demande de rôle organisateur n\'a pas pu être acceptée.')
            ->line('Si vous pensez qu\'il s\'agit d\'une erreur, contactez l\'administration.')
            ->action('Accéder à la plateforme', url('/dashboard'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'role_request_result',
            'status'  => $this->status,
            'message' => $this->status === 'approved'
                ? 'Votre demande de rôle organisateur a été approuvée.'
                : 'Votre demande de rôle organisateur a été refusée.',
        ];
    }
}