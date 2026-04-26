<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->date('date');
            $table->time('heure');
            $table->string('lieu');
            $table->string('categorie'); // Conférence, Atelier, Compétition, etc.
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->integer('places_disponibles');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
