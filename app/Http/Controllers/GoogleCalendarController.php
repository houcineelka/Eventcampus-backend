<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleCalendarController extends Controller
{
    /**
     * GET /api/google/calendar/auth-url
     * Retourne l'URL OAuth2 Google Calendar pour l'utilisateur connecté.
     */
    public function authUrl(Request $request)
    {
        $user = $request->user();

        // Stocker l'ID utilisateur dans le cache avec un token temporaire
        $stateToken = Str::uuid()->toString();
        Cache::put("calendar_auth_{$stateToken}", $user->id, now()->addMinutes(10));

        $params = [
            'client_id'     => config('services.google.client_id'),
            'redirect_uri'  => config('services.google_calendar.redirect'),
            'response_type' => 'code',
            'scope'         => 'https://www.googleapis.com/auth/calendar.events',
            'access_type'   => 'offline',
            'prompt'        => 'consent',
            'state'         => $stateToken,
        ];

        $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);

        return response()->json(['url' => $url]);
    }

    /**
     * GET /auth/google/calendar/callback
     * Reçoit le code OAuth2 de Google, échange contre un token, le stocke.
     */
    public function callback(Request $request)
    {
        $code  = $request->code;
        $state = $request->state;

        if (!$code || !$state) {
            return redirect(env('FRONTEND_URL') . '/student/profile?calendar=error');
        }

        $userId = Cache::pull("calendar_auth_{$state}");
        if (!$userId) {
            return redirect(env('FRONTEND_URL') . '/student/profile?calendar=error');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect(env('FRONTEND_URL') . '/student/profile?calendar=error');
        }

        // Échanger le code contre un access_token + refresh_token
        $response = Http::post('https://oauth2.googleapis.com/token', [
            'code'          => $code,
            'client_id'     => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'redirect_uri'  => config('services.google_calendar.redirect'),
            'grant_type'    => 'authorization_code',
        ]);

        if ($response->failed()) {
            return redirect(env('FRONTEND_URL') . '/student/profile?calendar=error');
        }

        $tokenData               = $response->json();
        $tokenData['created_at'] = time();

        $user->update(['google_calendar_token' => json_encode($tokenData)]);

        return redirect(env('FRONTEND_URL') . '/student/profile?calendar=success');
    }

    /**
     * GET /api/google/calendar/status
     * Indique si l'utilisateur a connecté son Google Calendar.
     */
    public function status(Request $request)
    {
        return response()->json([
            'connected' => !is_null($request->user()->google_calendar_token),
        ]);
    }

    /**
     * DELETE /api/google/calendar/disconnect
     * Supprime le token Google Calendar de l'utilisateur.
     */
    public function disconnect(Request $request)
    {
        $request->user()->update(['google_calendar_token' => null]);

        return response()->json(['message' => 'Google Calendar déconnecté.']);
    }
}
