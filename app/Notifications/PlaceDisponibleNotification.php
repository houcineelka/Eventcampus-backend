<?php
// app/Notifications/PlaceDisponibleNotification.php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PlaceDisponibleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Event $event) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    // ─── Email ────────────────────────────────────────────────────────

    public function toMail(object $notifiable): MailMessage
    {
        $eventUrl = config('app.url') . "/student/events/{$this->event->id}";

        return (new MailMessage)
            ->subject("🎉 Une place s'est libérée — {$this->event->titre}")
            ->greeting("Bonjour {$notifiable->name} !")
            ->line("Bonne nouvelle : une place vient de se libérer pour l'événement suivant :")
            ->line("**{$this->event->titre}**")
            ->line("📅 {$this->event->date} à {$this->event->heure}")
            ->line("📍 {$this->event->lieu}")
            ->action('Confirmer mon inscription', $eventUrl)
            ->line("⚠️ Cette place vous est réservée pendant **24 heures**. Passé ce délai, elle sera attribuée au prochain sur liste.")
            ->salutation("L'équipe EventCampus");
    }

    // ─── Notification in-app (base de données) ────────────────────────

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'place_disponible',
            'event_id'    => $this->event->id,
            'event_titre' => $this->event->titre,
            'message'     => "Une place s'est libérée pour \"{$this->event->titre}\".",
        ];
    }
}