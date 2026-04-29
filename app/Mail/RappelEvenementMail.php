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
        $titre      = htmlspecialchars($this->event->titre);
        $lieu       = htmlspecialchars($this->event->lieu);
        $date       = \Carbon\Carbon::parse($this->event->date)->locale('fr')->translatedFormat('l j F Y');
        $heure      = substr($this->event->heure, 0, 5);
        $clubNom    = htmlspecialchars($this->event->club?->nom ?? '');
        $clubEmoji  = $this->event->club?->emoji ?? '🎓';
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
        $unsubscribeUrl = $frontendUrl . '/preferences';

        $html = '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin:0;padding:0;background-color:#f4f4f4;font-family:Arial,sans-serif;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4;padding:40px 0;">
                <tr>
                    <td align="center">
                        <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

                            <!-- Header -->
                            <tr>
                                <td align="center" style="padding:32px 40px 16px;">
                                    <span style="font-size:24px;font-weight:bold;color:#1a1a2e;letter-spacing:1px;">Event<span style="color:#e63946;">Campus</span></span>
                                </td>
                            </tr>

                            <!-- Badge -->
                            <tr>
                                <td align="center" style="padding:8px 40px;">
                                    <span style="display:inline-block;background-color:#fff3cd;color:#856404;font-size:12px;font-weight:bold;padding:6px 16px;border-radius:20px;letter-spacing:1px;">
                                        🔔 RAPPEL — DEMAIN
                                    </span>
                                </td>
                            </tr>

                            <!-- Event Title -->
                            <tr>
                                <td align="center" style="padding:20px 40px 8px;">
                                    <h1 style="margin:0;font-size:26px;color:#1a1a2e;">' . $titre . '</h1>
                                </td>
                            </tr>

                            <!-- Divider -->
                            <tr>
                                <td style="padding:16px 40px;">
                                    <hr style="border:none;border-top:1px solid #eeeeee;">
                                </td>
                            </tr>

                            <!-- Event Details -->
                            <tr>
                                <td align="center" style="padding:0 40px 24px;">
                                    <table cellpadding="8" cellspacing="0">
                                        <tr>
                                            <td style="font-size:15px;color:#444;">📅 &nbsp;' . $date . '</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size:15px;color:#444;">🕐 &nbsp;' . $heure . '</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size:15px;color:#444;">📍 &nbsp;' . $lieu . '</td>
                                        </tr>
                                        <tr>
                                            <td style="font-size:15px;color:#444;">' . $clubEmoji . ' &nbsp;' . $clubNom . '</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Button -->
                            <tr>
                                <td align="center" style="padding:8px 40px 32px;">
                                    <a href="' . $frontendUrl . '/events" style="display:inline-block;background-color:#1a1a2e;color:#ffffff;text-decoration:none;padding:14px 36px;border-radius:8px;font-size:15px;font-weight:bold;">
                                        Voir l\'événement
                                    </a>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td align="center" style="padding:16px 40px 32px;border-top:1px solid #eeeeee;">
                                    <p style="font-size:12px;color:#999;margin:0 0 8px;">
                                        Vous recevez cet email car vous êtes inscrit à cet événement sur EventCampus.
                                    </p>
                                    <a href="' . $unsubscribeUrl . '" style="font-size:12px;color:#999;">
                                        Se désinscrire des rappels
                                    </a>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>';

        return new Content(htmlString: $html);
    }

    public function attachments(): array
    {
        return [];
    }
}
