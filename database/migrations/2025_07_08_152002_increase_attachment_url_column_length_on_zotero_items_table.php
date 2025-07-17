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
        Schema::table('zotero_items', function (Blueprint $table) {
            // Change la colonne 'attachment_url' de 'string' à 'text'
            // 'text' peut stocker des chaînes de caractères beaucoup plus longues que 'string' (VARCHAR).
            $table->text('attachment_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zotero_items', function (Blueprint $table) {
            // Pour le rollback, on la remet à 'string(255)'.
            // Attention : Si des URLs plus longues ont été stockées, elles seront tronquées au rollback.
            $table->string('attachment_url', 255)->nullable()->change();
        });
    }
};