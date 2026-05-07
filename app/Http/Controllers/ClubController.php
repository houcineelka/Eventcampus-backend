<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Notifications\NouvelleAdhesionNotification;
use Illuminate\Http\Request;

class ClubController extends Controller
{
    /**
     * POST /api/clubs
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom'         => 'required|string|max:255',
            'description' => 'required|string',
            'categorie'   => 'required|string|max:100',
            'emoji'       => 'nullable|string|max:10',
            'logo'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $club = Club::create([
            'nom'         => $request->nom,
            'description' => $request->description,
            'categorie'   => $request->categorie,
            'emoji'       => $request->emoji ?? '🎓',
            'createur_id' => $request->user()->id,
            'statut'      => 'en_attente',
            'logo'        => $logoPath,
        ]);

        $club->logo_url = $logoPath ? asset('storage/' . $logoPath) : null;

        return response()->json([
            'message' => 'Club créé avec succès. Il est en attente de validation.',
            'club'    => $club,
        ], 201);
    }

    /**
     * GET /api/clubs
     */
    public function index(Request $request)
    {
        $query = Club::withCount('membres');

        if ($request->has('categorie') && $request->categorie !== 'Tous') {
            $query->where('categorie', $request->categorie);
        }

        $clubs = $query->latest()->get();

        $userId = $request->user()->id;
        $clubs->each(function ($club) use ($userId) {
            $club->is_member = $club->membres->contains($userId);
        });

        return response()->json($clubs);
    }

    /**
     * GET /api/clubs/{id}
     */
 public function show(Request $request, $id)
{
    $club = Club::with([
            'membres' => function($q) {
                $q->select('users.id', 'prenom', 'nom')
                  ->withPivot('role');
            }
        ])
        ->withCount('membres')
        ->findOrFail($id);

    $club->is_member = $club->membres
        ->contains('id', $request->user()->id);

    return response()->json($club);
}



    /**
     * GET /api/profil/clubs
     */
    public function mesClubs(Request $request)
    {
        $userId = $request->user()->id;

        $clubs = Club::withCount('membres')
            ->whereHas('membres', fn($q) => $q->where('user_id', $userId))
            ->latest()
            ->get()
            ->map(fn($club) => [
                'id'            => $club->id,
                'nom'           => $club->nom,
                'description'   => $club->description,
                'categorie'     => $club->categorie,
                'emoji'         => $club->emoji,
                'membres_count' => $club->membres_count,
                'is_member'     => true,
            ]);

        return response()->json($clubs);
    }

    /**
     * POST /api/adhesions
     */
    public function adherer(Request $request)
    {
        $request->validate([
            'club_id' => 'required|exists:clubs,id',
        ]);

        $club   = Club::findOrFail($request->club_id);
        $userId = $request->user()->id;

        if ($club->membres()->where('user_id', $userId)->exists()) {
            return response()->json(['message' => 'Vous êtes déjà membre de ce club.'], 409);
        }

        $club->membres()->attach($userId, ['role' => 'membre']);

        $club->loadMissing('createur');
        if ($club->createur) {
            $club->createur->notify(new NouvelleAdhesionNotification($club, $request->user()));
        }

        return response()->json(['message' => 'Vous avez rejoint le club avec succès.'], 201);
    }

    /**
     * POST /api/clubs/{id}/leave
     */
    public function leave(Request $request, $id)
    {
        $club   = Club::findOrFail($id);
        $userId = $request->user()->id;

        if (!$club->membres()->where('user_id', $userId)->exists()) {
            return response()->json(['message' => 'Vous n\'êtes pas membre de ce club.'], 409);
        }

        $club->membres()->detach($userId);

        return response()->json(['message' => 'Vous avez quitté le club avec succès.']);
    }
}