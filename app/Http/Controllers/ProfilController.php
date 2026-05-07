<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use App\Models\EventWaitlist;

class ProfilController extends Controller
{
    /**
     * GET /api/profil/inscriptions
     * Retourne les inscriptions à venir et l'historique de l'étudiant connecté.
     */
    public function inscriptions(Request $request): JsonResponse
    {
        $user = $request->user();
        $now  = Carbon::today();

        // ── 1. Inscriptions confirmées (table pivot event_user) ──────────
        $participations = $user->eventParticipations()
            ->select('events.id', 'events.titre', 'events.date',
                     'events.heure', 'events.lieu', 'events.club_id')
            ->with('club:id,nom')
            ->get()
            ->map(fn($event) => [
                'id'        => $event->id,
                'titre'     => $event->titre,
                'date'      => $event->date,
                'heure'     => $event->heure,
                'lieu'      => $event->lieu,
                'club_name' => $event->club->nom ?? null,
                'statut'    => 'registered',
                'position'  => null,
            ]);

        // ── 2. Liste d'attente active ────────────────────────────────────
        $waitlist = EventWaitlist::where('user_id', $user->id)
            ->where('statut', 'en_attente')
            ->with('event:id,titre,date,heure,lieu,club_id', 'event.club:id,nom')
            ->get()
            ->map(fn($entry) => [
                'id'        => $entry->event->id,
                'titre'     => $entry->event->titre,
                'date'      => $entry->event->date,
                'heure'     => $entry->event->heure,
                'lieu'      => $entry->event->lieu,
                'club_name' => $entry->event->club->nom ?? null,
                'statut'    => 'waitlisted',
                'position'  => $entry->position,
            ]);

        // ── 3. Séparer passés / à venir ─────────────────────────────────
        // Upcoming = confirmés futurs + toute liste d'attente
        $upcoming = $participations
            ->filter(fn($e) => Carbon::parse($e['date'])->gte($now))
            ->values()
            ->concat($waitlist->values());

        // Past = uniquement les confirmés passés
        $past = $participations
            ->filter(fn($e) => Carbon::parse($e['date'])->lt($now))
            ->values();

        return response()->json([
            'upcoming' => $upcoming,
            'past'     => $past,
        ]);
    }
}