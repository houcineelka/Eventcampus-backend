<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Http\Request;

class ClubController extends Controller
{
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
     * POST /api/clubs/{id}/join
     */
    public function join(Request $request, $id)
    {
        $club = Club::findOrFail($id);
        $userId = $request->user()->id;

        if ($club->membres()->where('user_id', $userId)->exists()) {
            return response()->json(['message' => 'Vous êtes déjà membre de ce club.'], 409);
        }

        $club->membres()->attach($userId, ['role' => 'membre']);

        return response()->json(['message' => 'Vous avez rejoint le club avec succès.']);
    }

    /**
     * POST /api/clubs/{id}/leave
     */
    public function leave(Request $request, $id)
    {
        $club = Club::findOrFail($id);
        $userId = $request->user()->id;

        if (!$club->membres()->where('user_id', $userId)->exists()) {
            return response()->json(['message' => 'Vous n\'êtes pas membre de ce club.'], 409);
        }

        $club->membres()->detach($userId);

        return response()->json(['message' => 'Vous avez quitté le club.']);
    }
    
}