<?php
// Branch 1: Modèle Event mis à jour avec support liste d'attente
// Fichier: app/Models/Event.php

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
        'places_disponibles',  // places restantes (dynamique)
        'capacite_max',        // capacité totale fixe
    ];

    protected $appends = ['inscrits', 'est_complet'];

    // ─── Relations ───────────────────────────────────────────────────

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_user')
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

    // ─── Accesseurs calculés ─────────────────────────────────────────

    /**
     * Nombre d'inscrits confirmés
     */
    public function getInscritsAttribute(): int
    {
        return $this->participants()->count();
    }

    /**
     * L'événement est-il complet ?
     */
    public function getEstCompletAttribute(): bool
    {
        return $this->places_disponibles <= 0;
    }

    // ─── Méthodes utilitaires ────────────────────────────────────────

    /**
     * Vérifie si un user est inscrit à l'événement
     */
    public function estInscrit(int $userId): bool
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }

    /**
     * Vérifie si un user est en liste d'attente
     */
    public function estEnListeAttente(int $userId): bool
    {
        return $this->listeAttente()->where('user_id', $userId)->exists();
    }

    /**
     * Rang d'un user dans la liste d'attente (null si pas en liste)
     */
    public function rangListeAttente(int $userId): ?int
    {
        $entry = $this->listeAttente()->where('user_id', $userId)->first();
        return $entry?->position;
    }

    /**
     * Prochain rang disponible dans la liste d'attente
     */
    public function prochainRang(): int
    {
        $max = $this->tousListeAttente()
                    ->where('statut', 'en_attente')
                    ->max('position');
        return ($max ?? 0) + 1;
    }
}