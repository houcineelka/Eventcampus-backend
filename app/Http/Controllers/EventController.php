<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Notifications\NouvelleInscriptionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * GET /api/events
     */
    public function index(Request $request)
    {
        $query = Event::with('club');

        // Filtres optionnels
        if ($request->has('categorie') && $request->categorie !== 'Tous') {
            $query->where('categorie', $request->categorie);
        }

        if ($request->has('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        $events = $query->latest()->get();

        // Utilisation de formatEvent pour chaque événement
        $formattedEvents = $events->map(function ($event) {
            return $this->formatEvent($event, false);
        });

        return response()->json($formattedEvents);
    }

    /**
     * GET /api/events/{id}
     */
    public function show(Request $request, $id)
    {
        $event = Event::with(['club', 'participants'])
            ->findOrFail($id);

        // Appel de formatEvent avec $detailed = true
        return response()->json($this->formatEvent($event, true));
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
        $event = Event::findOrFail($request->event_id);

        if ($event->participants()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Vous êtes déjà inscrit à cet événement.'], 409);
        }

        // Logique de gestion de la liste d'attente ou inscription directe
        if ($event->places_disponibles !== null && $event->places_disponibles <= 0) {
            // Ici, vous devriez avoir votre logique pour ajouter à la liste d'attente
            // Exemple : $event->listeAttente()->create(['user_id' => $user->id]);
            return response()->json(['message' => 'Ajouté à la liste d\'attente.'], 200);
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

    /**
     * Formate l'événement pour la réponse JSON
     */
    private function formatEvent(Event $event, bool $detailed = false): array
    {
        $user = Auth::user() ?? auth('api')->user();
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
            'inscrits'            => $event->inscrits ?? $event->participants()->count(),
            'club_id'             => $event->club_id,
            'club_name'           => $event->club?->nom,
            // Statut de l'utilisateur connecté
            'is_registered'       => $isRegistered,
            'is_waitlisted'       => $isWaitlisted,
            'waitlist_position'   => $waitlistPos,
            // NOUVEAU — Branche 3 : Total de la liste d'attente
            'waitlist_total'      => $event->listeAttente()->count(),
        ];

        if ($detailed) {
            $data['club_members_count'] = $event->club?->membres()->count() ?? 0;
            $data['club_events_count']  = $event->club?->evenements()->count() ?? 0;
            
            
            // NOUVEAU — Branche 3 : Détails de la liste d'attente pour la vue détaillée
            $data['waitlist_details'] = $event->listeAttente()
                ->with('user:id,name,email')
                ->get()
                ->map(fn($w) => [
                    'position' => $w->position,
                    'name'     => $w->user->name ?? $w->user->prenom . ' ' . $w->user->nom,
                ]);
        }

        return $data;
    }
}