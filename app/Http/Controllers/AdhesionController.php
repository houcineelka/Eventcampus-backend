<?php

namespace App\Http\Controllers;

use App\Models\Adhesion;
use App\Models\Club;
use App\Notifications\AdhesionAccepteeNotification;
use App\Notifications\AdhesionRefuseeNotification;
use Illuminate\Http\Request;

class AdhesionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'club_id' => 'required|exists:clubs,id',
        ]);

        $userId = $request->user()->id;
        $clubId = $request->club_id;

        $existing = Adhesion::where('user_id', $userId)->where('club_id', $clubId)->first();

        if ($existing) {
            if ($existing->statut === 'en_attente') {
                return response()->json(['message' => 'Vous avez déjà une demande en cours pour ce club.'], 409);
            }
            if ($existing->statut === 'accepté') {
                $club = Club::findOrFail($clubId);
                if ($club->membres()->where('user_id', $userId)->exists()) {
                    return response()->json(['message' => 'Vous êtes déjà membre de ce club.'], 409);
                }
                // User left the club — allow re-application
                $existing->update(['statut' => 'en_attente']);
                return response()->json([
                    'message'  => 'Demande d\'adhésion envoyée avec succès. En attente de validation.',
                    'adhesion' => $existing,
                ], 201);
            }
            // statut === 'refusé' → allow re-application
            $existing->update(['statut' => 'en_attente']);
            return response()->json([
                'message'  => 'Demande d\'adhésion envoyée avec succès. En attente de validation.',
                'adhesion' => $existing,
            ], 201);
        }

        $adhesion = Adhesion::create([
            'user_id' => $userId,
            'club_id' => $clubId,
            'statut'  => 'en_attente',
        ]);

        return response()->json([
            'message'  => 'Demande d\'adhésion envoyée avec succès. En attente de validation.',
            'adhesion' => $adhesion,
        ], 201);
    }

    public function accepter(Request $request, $id)
    {
        $adhesion = Adhesion::findOrFail($id);
        $club = Club::findOrFail($adhesion->club_id);

        if ($club->createur_id !== $request->user()->id) {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à accepter cette demande.'], 403);
        }

        if ($adhesion->statut !== 'en_attente') {
            return response()->json(['message' => 'Cette demande a déjà été traitée.'], 409);
        }

        $adhesion->update(['statut' => 'accepté']);

        $club->membres()->syncWithoutDetaching([
            $adhesion->user_id => ['role' => 'membre']
        ]);

        $adhesion->user->notify(new AdhesionAccepteeNotification($club));

        return response()->json([
            'message'  => 'Demande d\'adhésion acceptée avec succès.',
            'adhesion' => $adhesion,
        ]);
    }

    public function refuser(Request $request, $id)
    {
        $adhesion = Adhesion::findOrFail($id);
        $club = Club::findOrFail($adhesion->club_id);

        if ($club->createur_id !== $request->user()->id) {
            return response()->json(['message' => 'Vous n\'êtes pas autorisé à refuser cette demande.'], 403);
        }

        if ($adhesion->statut !== 'en_attente') {
            return response()->json(['message' => 'Cette demande a déjà été traitée.'], 409);
        }

        $adhesion->update(['statut' => 'refusé']);

        $adhesion->user->notify(new AdhesionRefuseeNotification($club));

        return response()->json([
            'message'  => 'Demande d\'adhésion refusée.',
            'adhesion' => $adhesion,
        ]);
    }

    /**
     * List adhesions.
     * - Organiser/admin: returns adhesions for clubs they created.
     * - Others: returns adhesions made by the current user.
     * Query params:
     *  - statut: pending|accepted|refused
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $statut = $request->query('statut');

        $map = [
            'pending'  => 'en_attente',
            'accepted' => 'accepté',
            'refused'  => 'refusé',
        ];

        if ($statut && isset($map[$statut])) {
            $statut = $map[$statut];
        } else {
            $statut = null;
        }

        if (in_array($user->role, ['organisateur', 'admin'])) {
            $query = Adhesion::whereHas('club', function ($q) use ($user) {
                $q->where('createur_id', $user->id);
            });
        } else {
            $query = Adhesion::where('user_id', $user->id);
        }

        if ($statut) {
            $query->where('statut', $statut);
        }

        if ($request->has('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        $adhesions = $query->with(['user:id,prenom,nom,name,email', 'club:id,nom,createur_id'])->get();

        return response()->json(['adhesions' => $adhesions]);
    }
}
