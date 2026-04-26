<?php

namespace App\Http\Controllers;

use App\Models\Event;
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
     * POST /api/events/{id}/register
     */
    public function register(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $userId = $request->user()->id;

        // Check if already registered
        if ($event->participants()->where('user_id', $userId)->exists()) {
            return response()->json(['message' => 'Vous êtes déjà inscrit à cet événement.'], 409);
        }

        // Check available spots
        $registeredCount = $event->participants()->count();
        if ($registeredCount >= $event->places_disponibles) {
            return response()->json(['message' => 'Cet événement est complet.'], 409);
        }

        $event->participants()->attach($userId);

        return response()->json(['message' => 'Vous avez été inscrit à l\'événement avec succès.']);
    }

    /**
     * POST /api/events/{id}/unregister
     */
    public function unregister(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $userId = $request->user()->id;

        if (!$event->participants()->where('user_id', $userId)->exists()) {
            return response()->json(['message' => 'Vous n\'êtes pas inscrit à cet événement.'], 409);
        }

        $event->participants()->detach($userId);

        return response()->json(['message' => 'Vous avez été désinscrit de l\'événement.']);
    }
}
