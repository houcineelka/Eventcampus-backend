<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_waitlists', function (Blueprint $table) {
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('position')->default(0);
            $table->enum('statut', ['en_attente', 'promu', 'annule'])->default('en_attente');
            $table->timestamp('notifie_at')->nullable();

            $table->unique(['event_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('event_waitlists', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn(['event_id', 'user_id', 'position', 'statut', 'notifie_at']);
        });
    }
};