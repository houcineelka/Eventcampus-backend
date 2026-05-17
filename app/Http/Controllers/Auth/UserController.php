<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function me()
    {
        return response()->json([
            'user' => auth('api')->user(),
        ]);
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Déconnecté avec succès.']);
    }

    public function activateRole(Request $request, $id, $role)
    {
        $allowed = ['etudiant', 'organisateur', 'admin'];

        if (!in_array($role, $allowed)) {
            return response()->json(['message' => 'Rôle invalide.'], 422);
        }

        $user = User::findOrFail($id);
        $user->update(['role' => $role]);

        return response()->json([
            'message' => "Rôle de l'utilisateur mis à jour avec succès.",
            'user'    => $user,
        ]);
    }

    public function deactivateRole(Request $request, $id, $role)
    {
        $allowed = ['etudiant', 'organisateur', 'admin'];

        if (!in_array($role, $allowed)) {
            return response()->json(['message' => 'Rôle invalide.'], 422);
        }

        $user = User::findOrFail($id);

        if ($user->role !== $role) {
            return response()->json(['message' => 'L\'utilisateur n\'a pas ce rôle.'], 409);
        }

        $user->update(['role' => 'etudiant']);

        // EP-173 — le token JWT expirera naturellement. Le rôle étant déjà
        // changé en base, toute route protégée par role:organisateur bloquera
        // immédiatement l'accès même avec un token encore valide.

        return response()->json([
            'message' => "Rôle désactivé. L'utilisateur a été rétabli en tant qu'étudiant.",
            'user'    => $user->fresh(),
        ]);
    }

    public function desactiver(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Vous ne pouvez pas désactiver votre propre compte.'], 403);
        }

        $user->update(['role' => 'banni']);

        // EP-133 — le middleware CheckRole bloque immédiatement toute requête
        // de cet utilisateur avec le message "Votre compte est banni."

        return response()->json([
            'message' => 'Compte désactivé avec succès.',
            'user'    => $user->fresh(),
        ]);
    }

    public function updatePreferences(Request $request)
    {
        $request->validate([
            'email_reminders' => 'required|boolean',
        ]);

        $user = auth('api')->user();
        $user->update(['email_reminders' => $request->email_reminders]);

        return response()->json([
            'message'          => 'Préférences mises à jour avec succès.',
            'email_reminders'  => $user->email_reminders,
        ]);
    }
}
