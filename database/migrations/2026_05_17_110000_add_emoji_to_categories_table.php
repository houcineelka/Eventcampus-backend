<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('emoji', 20)->nullable()->after('nom');
        });

        $defaults = [
            'Conférence'      => '🎤',
            'Atelier'         => '🎨',
            'Soirée'          => '🎭',
            'Réunion de club' => '👥',
            'Hackathon'       => '👨‍💻',
            'Environnement'   => '🌱',
        ];

        foreach ($defaults as $nom => $emoji) {
            DB::table('categories')
                ->whereRaw('LOWER(nom) = ?', [mb_strtolower($nom)])
                ->update(['emoji' => $emoji]);
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('emoji');
        });
    }
};
