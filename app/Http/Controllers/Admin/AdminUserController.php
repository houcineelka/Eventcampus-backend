<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('nom', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate($request->get('per_page', 15));

        return response()->json($users);
    }

    public function show(User $user)
    {
        $user->load(['eventParticipations', 'waitlistEntries', 'conversations']);

        return response()->json([
            'user' => $user,
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:etudiant,organisateur,admin',
        ]);

        if ($user->role === $request->role) {
            return response()->json([
                'message' => 'L\'utilisateur a déjà ce rôle.',
            ], 409);
        }

        $user->update(['role' => $request->role]);

        return response()->json([
            'message' => 'Rôle mis à jour avec succès.',
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'prenom' => 'sometimes|string|max:255',
            'nom' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'role' => 'sometimes|in:etudiant,organisateur,admin',
        ]);

        $data = $request->only(['prenom', 'nom', 'email', 'role']);

        if ($request->has('password')) {
            $request->validate([
                'password' => 'required|string|min:8',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        if (isset($data['prenom'], $data['nom'])) {
            $data['name'] = $data['prenom'] . ' ' . $data['nom'];
        }

        $user->update($data);

        return response()->json([
            'message' => 'Utilisateur mis à jour avec succès.',
            'user' => $user,
        ]);
    }

    public function destroy(User $user)
    {
        $admin = auth('api')->user();

        if ($user->id === $admin->id) {
            return response()->json([
                'message' => 'Vous ne pouvez pas supprimer votre propre compte.',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès.',
        ]);
    }

    public function ban(User $user)
    {
        $admin = auth('api')->user();

        if ($user->id === $admin->id) {
            return response()->json([
                'message' => 'Vous ne pouvez pas bannir votre propre compte.',
            ], 403);
        }

        $user->update(['role' => 'banni']);

        return response()->json([
            'message' => 'Utilisateur banni avec succès.',
            'user' => $user,
        ]);
    }

    public function unban(User $user)
    {
        if ($user->role !== 'banni') {
            return response()->json([
                'message' => 'Cet utilisateur n\'est pas banni.',
            ], 400);
        }

        $user->update(['role' => 'etudiant']);

        return response()->json([
            'message' => 'Utilisateur débanni avec succès.',
            'user' => $user,
        ]);
    }

    public function stats()
    {
        return response()->json([
            'total' => User::count(),
            'etudiants' => User::where('role', 'etudiant')->count(),
            'organisateurs' => User::where('role', 'organisateur')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'bannis' => User::where('role', 'banni')->count(),
        ]);
    }
}
