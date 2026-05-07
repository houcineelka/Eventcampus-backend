<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Club extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'categorie',
        'emoji',
        'createur_id',
        'statut',
    ];

    // Relations
    public function createur()
    {
        return $this->belongsTo(User::class, 'createur_id');
    }

    public function membres()
    {
        return $this->belongsToMany(User::class, 'club_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function evenements()
    {
        return $this->hasMany(Event::class);
    } 
}