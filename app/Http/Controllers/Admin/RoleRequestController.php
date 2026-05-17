<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoleRequest;
use App\Notifications\RoleRequestResultNotification;

class RoleRequestController extends Controller
{
    public function index()
    {
        $requests = RoleRequest::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return response()->json(['data' => $requests]);
    }

    public function approve(RoleRequest $roleRequest)
    {
        if ($roleRequest->status !== 'pending') {
            return response()->json(['message' => 'Demande déjà traitée.'], 409);
        }

        $roleRequest->update(['status' => 'approved']);

        $user = $roleRequest->user;
        $user->update(['role' => 'organisateur']);

        /* EP-152 : notification
        $user->notify(new RoleRequestResultNotification('approved'));

        return response()->json([
            'message' => 'Demande approuvée. L\'utilisateur est maintenant organisateur.',
            'role_request' => $roleRequest->fresh(),
        ]); */
    }

    public function refuse(RoleRequest $roleRequest)
    {
        if ($roleRequest->status !== 'pending') {
            return response()->json(['message' => 'Demande déjà traitée.'], 409);
        }

        $roleRequest->update(['status' => 'refused']);

        /*EP-152 : notification
        $roleRequest->user->notify(new RoleRequestResultNotification('refused'));

        return response()->json([
            'message' => 'Demande refusée.',
            'role_request' => $roleRequest->fresh(),
        ]); */
    }
}