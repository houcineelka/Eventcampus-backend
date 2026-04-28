<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RappelEvenementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public Event $event
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rappel : ' . $this->event->titre . ' demain',
        );
    }

    public function content(): Content
    {
        $html = '
        <h2>Bonjour ' . htmlspecialchars($this->user->name) . ',</h2>
        <p>Nous vous rappelons que vous êtes inscrit à l\'événement suivant <strong>demain</strong> :</p>
        <ul>
            <li><strong>Événement :</strong> ' . htmlspecialchars($this->event->titre) . '</li>
            <li><strong>Date :</strong> ' . $this->event->date . '</li>
            <li><strong>Heure :</strong> ' . $this->event->heure . '</li>
            <li><strong>Lieu :</strong> ' . htmlspecialchars($this->event->lieu) . '</li>
        </ul>
        <p>Merci de votre participation et à demain !</p>
        <p>— L\'équipe EventCampus</p>
        ';

        return new Content(htmlString: $html);
    }

    public function attachments(): array
    {
        return [];
    }
}
