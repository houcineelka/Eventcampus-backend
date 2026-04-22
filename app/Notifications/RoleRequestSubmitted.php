<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RoleRequestSubmitted extends Notification
{
    use Queueable;

    public function __construct(public \App\Models\RoleRequest $roleRequest)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'role_request_id'     => $this->roleRequest->id,
            'name'                => $this->roleRequest->name,
            'email'               => $this->roleRequest->email,
            'is_existing_student' => $this->roleRequest->is_existing_student,
            'status'              => $this->roleRequest->status,
            'message'             => 'Nouvelle demande de rôle organisateur de ' . $this->roleRequest->name,
        ];
    }
}
