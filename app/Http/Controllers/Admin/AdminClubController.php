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
     * PUT /api/admin/clubs/{id}/valider
     */
    public function valider($id)
    {
        $club = Club::findOrFail($id);
        $club->update(['statut' => 'validé']);

        return response()->json(['message' => 'Club approuvé avec succès.']);
    }

    /**
     * PUT /api/admin/clubs/{id}/refuser
     */
    public function refuser($id)
    {
        $club = Club::findOrFail($id);
        $club->update(['statut' => 'rejeté']);

        return response()->json(['message' => 'Club rejeté.']);
    }

    /**
     * GET /api/admin/clubs/validated
     */
    public function validated()
    {
        $clubs = Club::with('createur:id,name,email')
            ->where('statut', 'validé')
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
     * PUT /api/admin/clubs/{id}/suspendre
     */
    public function suspendre($id)
    {
        $club = Club::findOrFail($id);
        $club->update(['statut' => 'suspendu']);

        return response()->json(['message' => 'Club suspendu avec succès.']);
    }

    /**
     * GET /api/admin/clubs/suspended
     */
    public function suspended()
    {
        $clubs = Club::with('createur:id,name,email')
            ->where('statut', 'suspendu')
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
     * PUT /api/admin/clubs/{id}/reactiver
     */
    public function reactiver($id)
    {
        $club = Club::findOrFail($id);
        $club->update(['statut' => 'validé']);

        return response()->json(['message' => 'Club réactivé avec succès.']);
    }

    /**
     * DELETE /api/admin/clubs/{id}
     */
    public function destroy($id)
    {
        $club = Club::findOrFail($id);
        $club->delete();

        return response()->json(['message' => 'Club supprimé avec succès.']);
    }
}
