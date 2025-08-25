<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Supprime l'ancienne table si elle existe
        Schema::dropIfExists('refresh_tokens');

        // Recrée la table propre
        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->id();

            // Ton modèle User utilise la table 'users_2025'
            $table->foreignId('user_id')
                  ->constrained('users_2025')
                  ->cascadeOnDelete();

            // Nouveau format stateful
            $table->string('token_id', 64)->unique();   // identifiant public (UUID)
            $table->string('token_hash', 255);          // Hash::make(secret)

            $table->timestamp('expires_at');
            $table->timestamp('revoked_at')->nullable();

            // Confort / audit (optionnel)
            $table->string('user_agent', 191)->nullable();
            $table->string('ip', 64)->nullable();

            $table->timestamps();

            $table->index(['user_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refresh_tokens');
    }
};
