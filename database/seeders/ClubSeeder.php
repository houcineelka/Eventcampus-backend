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
        $etudiant = User::create([
            'prenom'   => 'Aymane',
            'nom'      => 'Test',
            'name'     => 'Aymane Test',
            'email'    => 'aymane@univ.ma',
            'password' => Hash::make('password'),
            'role'     => 'etudiant',
        ]);

        $orga = User::create([
            'prenom'   => 'Sara',
            'nom'      => 'Benali',
            'name'     => 'Sara Benali',
            'email'    => 'sara@univ.ma',
            'password' => Hash::make('password'),
            'role'     => 'organisateur',
        ]);

        $clubs = [
            ['nom' => 'Club Tech & Innovation',      'description' => 'Hackathons, conférences et ateliers de développement.', 'categorie' => 'Technologie',  'emoji' => '💡', 'createur_id' => $orga->id],
            ['nom' => 'Club Théâtre & Expression',   'description' => 'Ateliers d\'improvisation, pièces de théâtre.',         'categorie' => 'Culture',       'emoji' => '🎭', 'createur_id' => $orga->id],
            ['nom' => 'Club Environnement',          'description' => 'Actions de sensibilisation, nettoyage du campus.',      'categorie' => 'Environnement', 'emoji' => '🌱', 'createur_id' => $orga->id],
            ['nom' => 'Club Musique',                'description' => 'Sessions de jam, concerts acoustiques.',                'categorie' => 'Art',           'emoji' => '🎵', 'createur_id' => $orga->id],
            ['nom' => 'Club Sportif Universitaire',  'description' => 'Tournois inter-facultés, compétitions régionales.',     'categorie' => 'Sport',         'emoji' => '⚽', 'createur_id' => $orga->id],
            ['nom' => 'Club Arts Plastiques',        'description' => 'Ateliers de peinture, dessin et photographie.',        'categorie' => 'Art',           'emoji' => '🎨', 'createur_id' => $orga->id],
            
        ];

        foreach ($clubs as $clubData) {
            $club = Club::create($clubData);
            $club->membres()->attach($orga->id,     ['role' => 'responsable']);
            $club->membres()->attach($etudiant->id, ['role' => 'membre']);
        }
    }
}