<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'date',
        'heure',
        'lieu',
        'categorie',
        'club_id',
        'places_disponibles',
    ];

    // Relations
    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_user')
                    ->withTimestamps();
    }
}
