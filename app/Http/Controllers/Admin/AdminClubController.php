<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;

class AdminClubController extends Controller
{
    /**
     * GET /api/admin/clubs/pending
     */
    public function pending()
    {
        $clubs = Club::with('createur:id,name,email')
            ->where('statut', 'en_attente')
            ->latest()
            ->get()
            ->map(fn($club) => [
                'id'          => $club->id,
                'nom'         => $club->nom,
                'description' => $club->description,
                'categorie'   => $club->categorie,
                'emoji'       => $club->emoji,
                'statut'      => $club->statut,
                'created_at'  => $club->created_at,
                'createur'    => $club->createur ? [
                    'id'    => $club->createur->id,
                    'name'  => $club->createur->name,
                    'email' => $club->createur->email,
                ] : null,
            ]);

        return response()->json($clubs);
    }

    /**
     * PUT /api/admin/clubs/{id}/approve
     */
    public function approve($id)
    {
        $club = Club::findOrFail($id);
        $club->update(['statut' => 'approuve']);

        return response()->json(['message' => 'Club approuvé avec succès.']);
    }

    /**
     * PUT /api/admin/clubs/{id}/reject
     */
    public function reject($id)
    {
        $club = Club::findOrFail($id);
        $club->update(['statut' => 'rejete']);

        return response()->json(['message' => 'Club rejeté.']);
    }
}
