<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
public function register(RegisterRequest $request)
{
    $user = User::create([
        'prenom'   => $request->prenom,
        'nom'      => $request->nom,
        'name'     => $request->prenom . ' ' . $request->nom,
        'email'    => $request->email,
        'password' => $request->password,
        'role'     => $request->role,
    ]);

    $token = JWTAuth::fromUser($user);

    return response()->json([
        'user'  => $user,
        'token' => $token,
    ], 201);
}

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['message' => 'Email ou mot de passe incorrect.'], 401);
        }

        return response()->json([
            'user' => auth('api')->user(),
            'token' => $token,
        ]);
    }
}
