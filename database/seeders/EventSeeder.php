<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Club;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $clubs = Club::all();

        if ($clubs->isEmpty()) {
            return;
        }

        $events = [
            // Événements pour Club Tech & Innovation
            [
                'titre' => 'Conférence IA et Machine Learning',
                'description' => 'Une conférence approfondie sur les dernières avancées en intelligence artificielle et apprentissage automatique.',
                'date' => '2026-04-28',
                'heure' => '14:00',
                'date_fin' => '2026-04-28',
                'heure_fin' => '16:00',
                'lieu' => 'Amphi A',
                'categorie' => 'Conférence',
                'club_id' => $clubs->where('nom', 'Club Tech & Innovation')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 100,
                'places_disponibles' => 100,
                'statut' => 'Accepté',
                'user_id' => $clubs->where('nom', 'Club Tech & Innovation')->first()->createur_id ?? 1,
            ],
            [
                'titre' => 'Hackathon 24h',
                'description' => 'Un hackathon de 24 heures où les équipes créent des solutions innovantes. Repas et accès WiFi fournis.',
                'date' => '2026-05-15',
                'heure' => '09:00',
                'date_fin' => '2026-05-16',
                'heure_fin' => '09:00',
                'lieu' => 'Salle Informatique 1-2',
                'categorie' => 'Hackathon',
                'club_id' => $clubs->where('nom', 'Club Tech & Innovation')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 100,
                'places_disponibles' => 100,
                'statut' => 'Accepté',
                'user_id' => $clubs->where('nom', 'Club Tech & Innovation')->first()->createur_id ?? 1,
            ],
            [
                'titre' => 'Atelier Web Development avec React',
                'description' => 'Apprenez les bases de React et créez votre première application web interactive.',
                'date' => '2026-05-02',
                'heure' => '16:00',
                'date_fin' => '2026-05-02',
                'heure_fin' => '18:00',
                'lieu' => 'Labo Informatique C',
                'categorie' => 'Atelier',
                'club_id' => $clubs->where('nom', 'Club Tech & Innovation')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 30,
                'places_disponibles' => 30,
                'statut' => 'Accepté',
                'user_id' => $clubs->where('nom', 'Club Tech & Innovation')->first()->createur_id ?? 1,
            ],

            // Événements pour Club Théâtre & Expression
            [
                'titre' => 'Atelier d\'Improvisation Théâtrale',
                'description' => 'Explorez l\'improvisation théâtrale à travers des jeux et exercices amusants. Aucune expérience requise!',
                'date' => '2026-04-30',
                'heure' => '18:00',
                'date_fin' => '2026-04-30',
                'heure_fin' => '20:00',
                'lieu' => 'Studio Théâtre',
                'categorie' => 'Atelier',
                'club_id' => $clubs->where('nom', 'Club Théâtre & Expression')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 25,
                'places_disponibles' => 25,
                'statut' => 'Accepté',
                'user_id' => $clubs->where('nom', 'Club Théâtre & Expression')->first()->createur_id ?? 2,
            ],
            [
                'titre' => 'Représentation Théâtrale: La Métamorphose',
                'description' => 'Découvrez notre adaptation moderne de La Métamorphose de Kafka. Une production entièrement réalisée par les étudiants.',
                'date' => '2026-05-20',
                'heure' => '20:00',
                'date_fin' => '2026-05-20',
                'heure_fin' => '22:00',
                'lieu' => 'Auditorium Principal',
                'categorie' => 'Soirée',
                'club_id' => $clubs->where('nom', 'Club Théâtre & Expression')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 100,
                'places_disponibles' => 100,
                'statut' => 'Accepté',
                'user_id' => $clubs->where('nom', 'Club Théâtre & Expression')->first()->createur_id ?? 2,
            ],

            // Événements pour Club Environnement
            [
                'titre' => 'Nettoyage du Campus et Zone Verte',
                'description' => 'Rejoignez-nous pour nettoyer notre campus et planter de nouveaux arbres pour un environnement plus vert.',
                'date' => '2026-05-05',
                'heure' => '08:00',
                'date_fin' => '2026-05-05',
                'heure_fin' => '12:00',
                'lieu' => 'Campus Central',
                'categorie' => 'Environnement',
                'club_id' => $clubs->where('nom', 'Club Environnement')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 60,
                'places_disponibles' => 60,
                'statut' => 'Accepté',
                'user_id' => $clubs->where('nom', 'Club Environnement')->first()->createur_id ?? 3,
            ],
            [
                'titre' => 'Conférence: Changement Climatique et Solutions',
                'description' => 'Expert en environnement discute des impacts du changement climatique et des solutions durables possibles.',
                'date' => '2026-05-12',
                'heure' => '14:00',
                'date_fin' => '2026-05-12',
                'heure_fin' => '16:00',
                'lieu' => 'Amphi B',
                'categorie' => 'Conférence',
                'club_id' => $clubs->where('nom', 'Club Environnement')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 80,
                'places_disponibles' => 80,
                'statut' => 'Accepté',
                'user_id' => $clubs->where('nom', 'Club Environnement')->first()->createur_id ?? 3,
            ],

            // Événements pour Club Musique
            [
                'titre' => 'Session de Jam Session Acoustique',
                'description' => 'Une soirée décontractée où musiciens et mélomanes se retrouvent pour une jam session acoustique.',
                'date' => '2026-05-08',
                'heure' => '19:00',
                'date_fin' => '2026-05-08',
                'heure_fin' => '21:00',
                'lieu' => 'Studio Musique',
                'categorie' => 'Soirée',
                'club_id' => $clubs->where('nom', 'Club Musique')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 40,
                'places_disponibles' => 40,
                'statut' => 'Accepté',
                'user_id' => $clubs->where('nom', 'Club Musique')->first()->createur_id ?? 4,
            ],
            [
                'titre' => 'Atelier Production Musicale',
                'description' => 'Apprenez à produire votre propre musique avec les derniers logiciels DAW.',
                'date' => '2026-05-22',
                'heure' => '17:00',
                'date_fin' => '2026-05-22',
                'heure_fin' => '19:00',
                'lieu' => 'Labo Musique',
                'categorie' => 'Atelier',
                'club_id' => $clubs->where('nom', 'Club Musique')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 20,
                'places_disponibles' => 20,
                'statut' => 'Accepté',
                'user_id' => $clubs->where('nom', 'Club Musique')->first()->createur_id ?? 4,
            ],

            // Événements pour Club Sportif Universitaire
            [
                'titre' => 'Tournoi de Football Inter-Facultés',
                'description' => 'Rejoignez votre équipe de faculté dans ce grand tournoi de football annuel.',
                'date' => '2026-05-18',
                'heure' => '10:00',
                'date_fin' => '2026-05-18',
                'heure_fin' => '18:00',
                'lieu' => 'Terrain de Sport Principal',
                'categorie' => 'Hackathon',
                'club_id' => $clubs->where('nom', 'Club Sportif Universitaire')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 150,
                'places_disponibles' => 150,
                'statut' => 'Accepté',
                'user_id' => $clubs->where('nom', 'Club Sportif Universitaire')->first()->createur_id ?? 5,
            ],
            [
                'titre' => 'Entraînement de Basketball',
                'description' => 'Session d\'entraînement ouverte pour tous les niveaux. Matériel fourni.',
                'date' => '2026-05-03',
                'heure' => '17:00',
                'date_fin' => '2026-05-03',
                'heure_fin' => '19:00',
                'lieu' => 'Gymnase',
                'categorie' => 'Atelier',
                'club_id' => $clubs->where('nom', 'Club Sportif Universitaire')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 30,
                'places_disponibles' => 30,
                'statut' => 'Accepté',
                'user_id' => $clubs->where('nom', 'Club Sportif Universitaire')->first()->createur_id ?? 5,
            ],

            // Événements pour Club Arts Plastiques
            [
                'titre' => 'Exposition d\'Art Étudiant',
                'description' => 'Exposition des meilleures œuvres créées par nos étudiants en peinture, dessin et photographie.',
                'date' => '2026-05-10',
                'heure' => '10:00',
                'date_fin' => '2026-05-10',
                'heure_fin' => '18:00',
                'lieu' => 'Galerie du Campus',
                'categorie' => 'Atelier',
                'club_id' => $clubs->where('nom', 'Club Arts Plastiques')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 200,
                'places_disponibles' => 200,
                'statut' => 'Accepté',
                'user_id' => $clubs->where('nom', 'Club Arts Plastiques')->first()->createur_id ?? 6,
            ],
            [
                'titre' => 'Atelier de Photographie: Composition et Lumière',
                'description' => 'Maîtrisez les principes de la composition photographique et l\'utilisation créative de la lumière.',
                'date' => '2026-05-17',
                'heure' => '15:00',
                'date_fin' => '2026-05-17',
                'heure_fin' => '17:00',
                'lieu' => 'Studio Photo',
                'categorie' => 'Atelier',
                'club_id' => $clubs->where('nom', 'Club Arts Plastiques')->first()->id ?? $clubs->first()->id,
                'capacite_max' => 25,
                'places_disponibles' => 25,
                'statut' => 'Accepté',
                'user_id' => $clubs->where('nom', 'Club Arts Plastiques')->first()->createur_id ?? 6,
            ],
        ];

        foreach ($events as $eventData) {
            Event::create($eventData);
        }
    }
}
