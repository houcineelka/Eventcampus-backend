<?php

namespace App\Http\Controllers;

class StudentController extends Controller
{
    public function dashboard()
    {
        $user = auth('api')->user();

        return response()->json([
            'message' => 'Bienvenue sur votre espace étudiant.',
            'user' => $user,
        ]);
    }
}
