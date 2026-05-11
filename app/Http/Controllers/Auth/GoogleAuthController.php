<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect("{$frontendUrl}/login?error=google_auth_failed");
        }

        $nameParts = explode(' ', $googleUser->getName(), 2);
        $prenom = $nameParts[0] ?? '';
        $nom    = $nameParts[1] ?? '';

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            if (!$user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        } else {
            $user = User::create([
                'google_id' => $googleUser->getId(),
                'prenom'    => $prenom,
                'nom'       => $nom,
                'name'      => $googleUser->getName(),
                'email'     => $googleUser->getEmail(),
                'password'  => Str::random(24),
                'role'      => 'etudiant',
            ]);
        }

        $token    = JWTAuth::fromUser($user);
        $userJson = urlencode(json_encode($user));

        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        return redirect("{$frontendUrl}/auth/google/callback?token={$token}&user={$userJson}");
    }
}
