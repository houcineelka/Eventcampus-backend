<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Club;
use Illuminate\Database\Seeder;

class PendingEventSeeder extends Seeder
{
    public function run(): void
    {
        $clubs = Club::all();

        if ($clubs->isEmpty()) {
            return;
        }

        $events = [
            [
                'titre' => 'Semaine de l\'Innovation Technologique',
                'description' => 'Une semaine dédiée aux nouvelles technologies avec des ateliers, des conférences et des démonstrations.',
                'date' => '2026-06-10',
                'heure' => '09:00',
                'date_fin' => '2026-06-14',
                'heure_fin' => '17:00',
                'lieu' => 'Campus Central',
                'categorie' => 'Conférence',
                'club_id' => $clubs->where('nom', 'Club Tech & Innovation')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 200,
                'places_disponibles' => 200,
                'statut' => 'En attente',
                'user_id' => $clubs->where('nom', 'Club Tech & Innovation')->first()->createur_id ?? 1,
            ],
            [
                'titre' => 'Festival de Théâtre Étudiant',
                'description' => 'Un festival de trois jours mettant en scène les meilleures pièces de théâtre créées par les étudiants.',
                'date' => '2026-06-20',
                'heure' => '18:00',
                'date_fin' => '2026-06-22',
                'heure_fin' => '22:00',
                'lieu' => 'Auditorium Principal',
                'categorie' => 'Soirée',
                'club_id' => $clubs->where('nom', 'Club Théâtre & Expression')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 150,
                'places_disponibles' => 150,
                'statut' => 'En attente',
                'user_id' => $clubs->where('nom', 'Club Théâtre & Expression')->first()->createur_id ?? 2,
            ],
            [
                'titre' => 'Journée de Plantation d\'Arbres',
                'description' => 'Participez à la plantation de 500 arbres autour du campus pour contribuer à la biodiversité locale.',
                'date' => '2026-07-01',
                'heure' => '07:00',
                'date_fin' => '2026-07-01',
                'heure_fin' => '13:00',
                'lieu' => 'Périmètre Campus',
                'categorie' => 'Environnement',
                'club_id' => $clubs->where('nom', 'Club Environnement')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 80,
                'places_disponibles' => 80,
                'statut' => 'En attente',
                'user_id' => $clubs->where('nom', 'Club Environnement')->first()->createur_id ?? 3,
            ],
            [
                'titre' => 'Concert de Fin d\'Année',
                'description' => 'Un grand concert réunissant tous les groupes musicaux du campus pour célébrer la fin de l\'année.',
                'date' => '2026-07-15',
                'heure' => '20:00',
                'date_fin' => '2026-07-15',
                'heure_fin' => '23:30',
                'lieu' => 'Place du Campus',
                'categorie' => 'Soirée',
                'club_id' => $clubs->where('nom', 'Club Musique')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 300,
                'places_disponibles' => 300,
                'statut' => 'En attente',
                'user_id' => $clubs->where('nom', 'Club Musique')->first()->createur_id ?? 4,
            ],
            [
                'titre' => 'Marathon Universitaire 10K',
                'description' => 'Un marathon de 10 kilomètres ouvert à tous les étudiants et membres du personnel.',
                'date' => '2026-07-20',
                'heure' => '06:00',
                'date_fin' => '2026-07-20',
                'heure_fin' => '12:00',
                'lieu' => 'Circuit Campus',
                'categorie' => 'Compétition',
                'club_id' => $clubs->where('nom', 'Club Sportif Universitaire')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 250,
                'places_disponibles' => 250,
                'statut' => 'En attente',
                'user_id' => $clubs->where('nom', 'Club Sportif Universitaire')->first()->createur_id ?? 5,
            ],
            [
                'titre' => 'Exposition d\'Art Contemporain',
                'description' => 'Une exposition présentant les œuvres contemporaines réalisées par les étudiants en arts plastiques.',
                'date' => '2026-08-05',
                'heure' => '10:00',
                'date_fin' => '2026-08-10',
                'heure_fin' => '18:00',
                'lieu' => 'Galerie du Campus',
                'categorie' => 'Exposition',
                'club_id' => $clubs->where('nom', 'Club Arts Plastiques')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 100,
                'places_disponibles' => 100,
                'statut' => 'En attente',
                'user_id' => $clubs->where('nom', 'Club Arts Plastiques')->first()->createur_id ?? 6,
            ],
        ];

        foreach ($events as $eventData) {
            Event::create($eventData);
        }
    }
}
