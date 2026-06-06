<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequestRequest;
use App\Models\RoleRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RoleRequestController extends Controller
{
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $exists = User::where('email', $request->email)->exists();

        return response()->json([
            'exists' => $exists,
        ]);
    }

    public function store(StoreRoleRequestRequest $request)
    {
        if ($request->is_existing_student) {
            return $this->handleExistingStudent($request);
        }

        return $this->handleNewUser($request);
    }

    public function handleExistingStudent(StoreRoleRequestRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Aucun compte trouvé avec cet email.',
            ], 404);
        }

        $roleRequest = RoleRequest::create([
            'user_id'             => $user->id,
            'name'                => $request->name,
            'email'               => $request->email,
            'student_id'          => $request->student_id,
            'is_existing_student' => true,
            'status'              => 'pending',
        ]);

        return response()->json([
            'message'      => 'Votre demande de rôle organisateur a été soumise avec succès.',
            'role_request' => $roleRequest,
        ], 201);
    }

    public function handleNewUser(StoreRoleRequestRequest $request)
    {
        $user = User::create([
            'prenom'   => $request->prenom,
            'nom'      => $request->nom,
            'name'     => $request->name ?? trim($request->prenom . ' ' . $request->nom),
            'email'    => $request->email,
            'password' => $request->password,
            'role'     => 'etudiant',
        ]);

        $roleRequest = RoleRequest::create([
            'user_id'             => $user->id,
            'name'                => $request->name,
            'email'               => $request->email,
            'student_id'          => $request->student_id,
            'is_existing_student' => false,
            'status'              => 'pending',
        ]);

        return response()->json([
            'message'      => 'Votre compte a été créé et votre demande de rôle organisateur a été soumise avec succès.',
            'role_request' => $roleRequest,
        ], 201);
    }
}
