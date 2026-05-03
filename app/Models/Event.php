<?php
// app/Models/Event.php

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
        'date_fin',
        'heure_fin',
        'lieu',
        'categorie',
        'club_id',
        'places_disponibles',
        'capacite_max',
    ];

    protected $appends = ['inscrits', 'est_complet'];

    // ─── Relations ────────────────────────────────────────────────────

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    /**
     * MODIFIÉ : utilise le pivot custom EventUser
     * pour déclencher la promotion automatique au deleted()
     */
    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_user')
                    ->using(EventUser::class)   // ← AJOUT branche 5
                    ->withTimestamps();
    }

    public function listeAttente()
    {
        return $this->hasMany(EventWaitlist::class)
                    ->where('statut', 'en_attente')
                    ->orderBy('position');
    }

    public function tousListeAttente()
    {
        return $this->hasMany(EventWaitlist::class)->orderBy('position');
    }

    // ─── Accesseurs ───────────────────────────────────────────────────

    public function getInscritsAttribute(): int
    {
        return $this->participants()->count();
    }

    public function getEstCompletAttribute(): bool
    {
        return $this->places_disponibles <= 0;
    }

    // ─── Méthodes utilitaires ─────────────────────────────────────────

    public function estInscrit(int $userId): bool
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }

    public function estEnListeAttente(int $userId): bool
    {
        return $this->listeAttente()->where('user_id', $userId)->exists();
    }

    public function rangListeAttente(int $userId): ?int
    {
        $entry = $this->listeAttente()->where('user_id', $userId)->first();
        return $entry?->position;
    }

    public function prochainRang(): int
    {
        $max = $this->tousListeAttente()
                    ->where('statut', 'en_attente')
                    ->max('position');
        return ($max ?? 0) + 1;
    }
}