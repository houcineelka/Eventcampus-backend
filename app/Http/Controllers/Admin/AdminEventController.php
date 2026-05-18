<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class AdminEventController extends Controller
{
    /**
     * GET /api/admin/events/pending
     */
    public function pending()
    {
        $events = Event::with(['club:id,nom,emoji', 'club.createur:id,name,email'])
            ->where('statut', 'En attente')
            ->latest()
            ->get()
            ->map(fn($event) => [
                'id'          => $event->id,
                'titre'       => $event->titre,
                'description' => $event->description,
                'date'        => $event->date,
                'heure'       => $event->heure,
                'lieu'        => $event->lieu,
                'categorie'   => $event->categorie,
                'statut'      => $event->statut,
                'capacite_max' => $event->capacite_max,
                'inscrits'    => $event->inscrits,
                'club'        => $event->club ? [
                    'id'    => $event->club->id,
                    'nom'   => $event->club->nom,
                    'emoji' => $event->club->emoji,
                    'createur' => $event->club->createur ? [
                        'id'    => $event->club->createur->id,
                        'name'  => $event->club->createur->name,
                        'email' => $event->club->createur->email,
                    ] : null,
                ] : null,
                'created_at'  => $event->created_at,
            ]);

        return response()->json($events);
    }

    /**
     * GET /api/admin/events
     */
    public function index(Request $request)
    {
        $query = Event::with(['club:id,nom,emoji', 'club.createur:id,name,email']);

        if ($request->has('statut') && $request->statut !== 'Tous') {
            $query->where('statut', $request->statut);
        }

        $events = $query->latest()->get()->map(fn($event) => [
            'id'          => $event->id,
            'titre'       => $event->titre,
            'description' => $event->description,
            'date'        => $event->date,
            'heure'       => $event->heure,
            'lieu'        => $event->lieu,
            'categorie'   => $event->categorie,
            'statut'      => $event->statut,
            'capacite_max' => $event->capacite_max,
            'inscrits'    => $event->inscrits,
            'club'        => $event->club ? [
                'id'    => $event->club->id,
                'nom'   => $event->club->nom,
                'emoji' => $event->club->emoji,
                'createur' => $event->club->createur ? [
                    'id'    => $event->club->createur->id,
                    'name'  => $event->club->createur->name,
                    'email' => $event->club->createur->email,
                ] : null,
            ] : null,
            'created_at'  => $event->created_at,
            'updated_at'  => $event->updated_at,
        ]);

        return response()->json($events);
    }

    /**
     * PUT /api/admin/events/{id}/valider
     */
    public function valider($id)
    {
        $event = Event::findOrFail($id);

        if (in_array($event->statut, ['Validé', 'Accepté'])) {
            return response()->json(['message' => 'Cet événement est déjà validé.'], 400);
        }

        $event->update(['statut' => 'Validé']);

        return response()->json(['message' => 'Événement validé avec succès.']);
    }

    /**
     * PUT /api/admin/events/{id}/refuser
     */
    public function refuser($id)
    {
        $event = Event::findOrFail($id);

        if ($event->statut === 'Rejeté') {
            return response()->json(['message' => 'Cet événement est déjà rejeté.'], 400);
        }

        $event->update(['statut' => 'Rejeté']);

        return response()->json(['message' => 'Événement rejeté.']);
    }
}
