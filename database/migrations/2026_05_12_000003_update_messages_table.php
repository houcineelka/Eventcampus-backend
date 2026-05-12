<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('conversation_id')->nullable()->constrained('conversations')->cascadeOnDelete();
            $table->boolean('is_read')->default(false);
            $table->foreignId('receiver_id')->nullable()->constrained('users')->cascadeOnDelete();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeignKeyConstraints();
            $table->dropColumn(['conversation_id', 'is_read', 'receiver_id']);
            $table->dropIndex(['conversation_id', 'created_at']);
        });
    }
};
