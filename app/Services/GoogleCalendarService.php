<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class GoogleCalendarService
{
    /**
     * Ajoute un événement au Google Agenda de l'utilisateur.
     * Retourne true si succès, false sinon.
     */
    public function addEvent(User $user, Event $event): bool
    {
        if (!$user->google_calendar_token) {
            return false;
        }

        $tokenData   = json_decode($user->google_calendar_token, true);
        $accessToken = $this->getValidAccessToken($user, $tokenData);

        if (!$accessToken) {
            return false;
        }

        $startTime = $this->normalizeTime($event->heure ?? '09:00:00');
        $endTime   = $this->normalizeTime(
            $event->heure_fin ?? date('H:i:s', strtotime($startTime . ' +2 hours'))
        );

        $startDatetime = $event->date . 'T' . $startTime;
        $endDatetime   = ($event->date_fin ?? $event->date) . 'T' . $endTime;

        $calendarEvent = [
            'summary'     => $event->titre,
            'description' => $event->description ?? '',
            'location'    => $event->lieu ?? '',
            'start'       => ['dateTime' => $startDatetime, 'timeZone' => 'Africa/Tunis'],
            'end'         => ['dateTime' => $endDatetime,   'timeZone' => 'Africa/Tunis'],
        ];

        $response = Http::withToken($accessToken)
            ->post('https://www.googleapis.com/calendar/v3/calendars/primary/events', $calendarEvent);

        return $response->successful();
    }

    /**
     * Retourne un access token valide (rafraîchi si expiré).
     */
    private function getValidAccessToken(User $user, array $tokenData): ?string
    {
        $createdAt = $tokenData['created_at'] ?? 0;
        $expiresIn = $tokenData['expires_in'] ?? 3600;

        // Token encore valide
        if ((time() - $createdAt) < ($expiresIn - 60)) {
            return $tokenData['access_token'];
        }

        // Token expiré — tenter un refresh
        if (empty($tokenData['refresh_token'])) {
            $user->update(['google_calendar_token' => null]);
            return null;
        }

        $response = Http::post('https://oauth2.googleapis.com/token', [
            'client_id'     => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'refresh_token' => $tokenData['refresh_token'],
            'grant_type'    => 'refresh_token',
        ]);

        if ($response->failed()) {
            $user->update(['google_calendar_token' => null]);
            return null;
        }

        $newToken               = $response->json();
        $newToken['created_at'] = time();
        $newToken['refresh_token'] = $tokenData['refresh_token'];

        $user->update(['google_calendar_token' => json_encode($newToken)]);

        return $newToken['access_token'];
    }

    private function normalizeTime(string $time): string
    {
        // Accepte HH:MM ou HH:MM:SS
        return strlen($time) === 5 ? $time . ':00' : $time;
    }
}
