<?php

namespace Database\Seeders;
use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClubSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // Students
        // =========================
        $etudiant1 = User::create([
            'prenom'   => 'Aymane',
            'nom'      => 'Test',
            'name'     => 'Aymane Test',
            'email'    => 'aymane@univ.ma',
            'password' => Hash::make('password'),
            'role'     => 'etudiant',
        ]);

        $etudiant2 = User::create([
            'prenom'   => 'ayoub',
            'nom'      => 'ab',
            'name'     => 'ayoub ab',
            'email'    => 'ayoub@univ.ma',
            'password' => Hash::make('password'),
            'role'     => 'etudiant',
        ]);

        $etudiant3 = User::create([
            'prenom'   => 'Salma',
            'nom'      => 'Zahraoui',
            'name'     => 'Salma Zahraoui',
            'email'    => 'salma@univ.ma',
            'password' => Hash::make('password'),
            'role'     => 'etudiant',
        ]);

        // =========================
        // Organizers
        // =========================
        $orga1 = User::create([
            'prenom'   => 'Sara',
            'nom'      => 'Benali',
            'name'     => 'Sara Benali',
            'email'    => 'sara@univ.ma',
            'password' => Hash::make('password'),
            'role'     => 'organisateur',
        ]);

        $orga2 = User::create([
            'prenom'   => 'ahmad',
            'nom'      => 'aarab',
            'name'     => 'ahmad aarab',
            'email'    => 'ahmad@univ.ma',
            'password' => Hash::make('password'),
            'role'     => 'organisateur',
        ]);

        $orga3 = User::create([
            'prenom'   => 'Nadia',
            'nom'      => 'Kabbaj',
            'name'     => 'Nadia Kabbaj',
            'email'    => 'nadia@univ.ma',
            'password' => Hash::make('password'),
            'role'     => 'organisateur',
        ]);

        // =========================
        // Clubs
        // =========================
        $clubs = [
            [
                'nom' => 'Club Tech & Innovation',
                'description' => 'Hackathons, conférences et ateliers de développement.',
                'categorie' => 'Technologie',
                'emoji' => '💡',
                'createur_id' => $orga1->id
            ],
            [
                'nom' => 'Club Théâtre & Expression',
                'description' => 'Ateliers d\'improvisation, pièces de théâtre.',
                'categorie' => 'Culture',
                'emoji' => '🎭',
                'createur_id' => $orga1->id
            ],
            [
                'nom' => 'Club Environnement',
                'description' => 'Actions de sensibilisation, nettoyage du campus.',
                'categorie' => 'Environnement',
                'emoji' => '🌱',
                'createur_id' => $orga2->id
            ],
            [
                'nom' => 'Club Musique',
                'description' => 'Sessions de jam, concerts acoustiques.',
                'categorie' => 'Art',
                'emoji' => '🎵',
                'createur_id' => $orga2->id
            ],
            [
                'nom' => 'Club Sportif Universitaire',
                'description' => 'Tournois inter-facultés, compétitions régionales.',
                'categorie' => 'Sport',
                'emoji' => '⚽',
                'createur_id' => $orga3->id
            ],
            [
                'nom' => 'Club Arts Plastiques',
                'description' => 'Ateliers de peinture, dessin et photographie.',
                'categorie' => 'Art',
                'emoji' => '🎨',
                'createur_id' => $orga3->id
            ],
        ];

        foreach ($clubs as $clubData) {
            $club = Club::create($clubData);

            // Organizer becomes responsible
            $club->membres()->attach($clubData['createur_id'], [
                'role' => 'responsable'
            ]);

            // Add students
            $club->membres()->attach($etudiant1->id, [
                'role' => 'membre'
            ]);

            $club->membres()->attach($etudiant2->id, [
                'role' => 'membre'
            ]);

            $club->membres()->attach($etudiant3->id, [
                'role' => 'membre'
            ]);
        }
    }
}
