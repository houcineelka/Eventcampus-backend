<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['individuel', 'broadcast']);
            $table->foreignId('creator_id')->constrained('users')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->timestamps();

            $table->index('creator_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
