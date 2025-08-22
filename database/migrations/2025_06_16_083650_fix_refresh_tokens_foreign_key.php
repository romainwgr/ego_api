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
    Schema::table('refresh_tokens', function (Blueprint $table) {
        $table->dropForeign(['user_id']); // on supprime l'ancienne contrainte
        $table->foreign('user_id')
              ->references('id')
              ->on('users_2025')
              ->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('refresh_tokens', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->foreign('user_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
    });
}
};
