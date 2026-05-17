<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100)->unique();
            $table->timestamps();
        });

        // Insérer les catégories par défaut
        $defaults = ['Conférence', 'Atelier', 'Soirée', 'Réunion de club', 'Hackathon', 'Environnement'];
        foreach ($defaults as $nom) {
            DB::table('categories')->insert(['nom' => $nom, 'created_at' => now(), 'updated_at' => now()]);
        }

        // Importer les catégories déjà existantes dans la table events
        $existing = DB::table('events')
            ->whereNotNull('categorie')
            ->where('categorie', '!=', '')
            ->distinct()
            ->pluck('categorie');

        foreach ($existing as $nom) {
            $already = DB::table('categories')
                ->whereRaw('LOWER(nom) = ?', [mb_strtolower($nom)])
                ->exists();
            if (!$already) {
                DB::table('categories')->insert(['nom' => trim($nom), 'created_at' => now(), 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
