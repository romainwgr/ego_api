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
            // Modifiez la colonne 'title' pour utiliser 'text' au lieu de 'string'
            // 'text' ne limite pas la longueur comme VARCHAR(255)
            $table->text('title')->nullable()->change();

            // C'est aussi une bonne idée de faire la même chose pour 'publicationTitle'
            // car les titres de revues/conférences peuvent aussi être longs.
            $table->text('publicationTitle')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zotero_items', function (Blueprint $table) {
            // Pour revenir en arrière, on pourrait les redéfinir en string,
            // mais attention aux données qui pourraient être tronquées si elles dépassent 255 caractères.
            // Pour un rollback sûr, il est souvent préférable de ne pas tronquer les données existantes.
            // Ou de forcer un VARCHAR(255) avec un warning si nécessaire.
            $table->string('title', 255)->nullable()->change();
            $table->string('publicationTitle', 255)->nullable()->change();
        });
    }
};