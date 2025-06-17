<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('refresh_tokens', function (Blueprint $table) {
            $table->text('token')->change(); // ou string(null)->change() si tu veux toujours limiter
        });
    }

    public function down(): void
    {
        Schema::table('refresh_tokens', function (Blueprint $table) {
            $table->string('token', 64)->change(); // revert to old size
        });
    }
};

