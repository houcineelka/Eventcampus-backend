<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
