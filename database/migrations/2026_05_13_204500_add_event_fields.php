<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // Check if columns don't already exist before adding
            if (!Schema::hasColumn('events', 'date_fin')) {
                $table->date('date_fin')->nullable()->after('heure');
            }
            if (!Schema::hasColumn('events', 'heure_fin')) {
                $table->time('heure_fin')->nullable()->after('date_fin');
            }
            if (!Schema::hasColumn('events', 'capacite_max')) {
                $table->integer('capacite_max')->nullable()->after('places_disponibles');
            }
            if (!Schema::hasColumn('events', 'statut')) {
                $table->string('statut')->default('En attente')->after('capacite_max');
            }
            if (!Schema::hasColumn('events', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->after('club_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['date_fin', 'heure_fin', 'capacite_max', 'statut', 'user_id']);
        });
    }
};
