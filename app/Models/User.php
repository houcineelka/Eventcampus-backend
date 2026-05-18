<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

        protected $fillable = [
            'prenom',
            'nom',
            'name',
            'email',
            'password',
            'role',
            'email_reminders',
            'whatsapp_reminders',
            'phone',
            'google_id',
            'google_calendar_token',
        ];

    protected $hidden = [
        'password',
        'remember_token',
        'google_calendar_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'email_reminders'   => 'boolean',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
        ];
    }

    public function eventParticipations()
    {
        return $this->belongsToMany(Event::class, 'event_user')
                    ->withTimestamps();
    }

public function waitlistEntries()
    {
        return $this->hasMany(EventWaitlist::class);
    }

    public function messagesSent()
    {
        return $this->hasMany(Message::class, 'user_id');
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user')
                    ->withTimestamps();
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
}
