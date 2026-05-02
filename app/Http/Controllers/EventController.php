<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Notifications\NouvelleInscriptionNotification;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * GET /api/events
     */
    public function index(Request $request)
    {
        $query = Event::with('club')
                      ->withCount('participants');

        // Optional filters
        if ($request->has('categorie') && $request->categorie !== 'Tous') {
            $query->where('categorie', $request->categorie);
        }

        if ($request->has('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        $events = $query->latest()->get();

        // Add club_name and is_registered for each event
        $userId = $request->user()->id;
        $events->each(function ($event) use ($userId) {
            $event->club_name = $event->club->nom;
            $event->is_registered = $event->participants->contains($userId);
        });

        return response()->json($events);
    }

    /**
     * GET /api/events/{id}
     */
    public function show(Request $request, $id)
    {
        $event = Event::with([
                'club' => function($q) {
                    $q->select('id', 'nom');
                },
                'participants' => function($q) {
                    $q->select('users.id', 'prenom', 'nom');
                }
            ])
            ->withCount('participants')
            ->findOrFail($id);

        $event->club_name = $event->club->nom;
        $event->is_registered = $event->participants
            ->contains('id', $request->user()->id);

        return response()->json($event);
    }

    /**
     * POST /api/inscriptions
     */
    public function inscrire(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);

        $user  = auth('api')->user();
        $event = Event::withCount('participants')->findOrFail($request->event_id);

        if ($event->participants()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Vous êtes déjà inscrit à cet événement.'], 409);
        }

        if ($event->places_disponibles !== null && $event->places_disponibles <= 0) {
            return response()->json(['message' => 'Cet événement est complet.'], 422);
        }

        $event->participants()->attach($user->id);

        if ($event->places_disponibles !== null) {
            $event->decrement('places_disponibles');
        }

        $event->loadMissing('club.createur');
        $organisateur = $event->club->createur;
        if ($organisateur) {
            $organisateur->notify(new NouvelleInscriptionNotification($event, $user));
        }

        return response()->json(['message' => 'Inscription réussie.'], 201);
    }

        private function formatEvent(Event $event, bool $detailed = false): array
    {
        $user = Auth::user();
        $userId = $user?->id;
 
        $isRegistered = $userId ? $event->estInscrit($userId) : false;
        $isWaitlisted = $userId ? $event->estEnListeAttente($userId) : false;
        $waitlistPos  = $isWaitlisted ? $event->rangListeAttente($userId) : null;
 
        $data = [
            'id'                  => $event->id,
            'titre'               => $event->titre,
            'description'         => $event->description,
            'date'                => $event->date,
            'heure'               => $event->heure,
            'date_fin'            => $event->date_fin,
            'heure_fin'           => $event->heure_fin,
            'lieu'                => $event->lieu,
            'categorie'           => $event->categorie,
            'places_disponibles'  => $event->places_disponibles,
            'capacite_max'        => $event->capacite_max,
            'inscrits'            => $event->inscrits,
            'club_id'             => $event->club_id,
            'club_name'           => $event->club?->nom,
            // Statut de l'utilisateur connecté
            'is_registered'       => $isRegistered,
            'is_waitlisted'       => $isWaitlisted,
            'waitlist_position'   => $waitlistPos,
        ];
 
        if ($detailed) {
            $data['club_members_count'] = $event->club?->membres()->count() ?? 0;
            $data['club_events_count']  = $event->club?->events()->count() ?? 0;
        }
 
        return $data;
    }
}
