<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('refresh_tokens', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')
            ->constrained('users_2025')
            ->onDelete('cascade');
        // clé étrangère vers users, supprime les tokens si user supprimé

        $table->string('token', 64)->unique(); // refresh token (stocké sous forme hashée idéalement)
        $table->string('user_agent')->nullable(); // optionnel, pour info device navigateur
        $table->ipAddress('ip_address')->nullable(); // optionnel, pour info IP

        $table->boolean('revoked')->default(false); // pour marquer le token comme révoqué
        $table->timestamp('expires_at'); // date d'expiration du refresh token

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refresh_tokens');
    }
};
