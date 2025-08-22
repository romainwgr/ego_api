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
        Schema::create('zotero_items', function (Blueprint $table) {
            $table->id();
            $table->string('itemKey')->unique(); // La clé unique Zotero (ex: "QH6NAM34")
            $table->string('title')->nullable();
            $table->json('creators')->nullable(); // Pour stocker les auteurs (format JSON)
            $table->string('date')->nullable(); // Pour l'année de publication
            $table->string('attachment_url')->nullable(); // URL vers l'article/blog/DOI
            $table->string('itemType')->nullable(); // Type d'item (journalArticle, blogPost, etc.)
            $table->text('abstractNote')->nullable(); // Résumé de l'article
            $table->string('publicationTitle')->nullable(); // Titre du journal/blog/livre
            $table->timestamps(); // created_at et updated_at (Laravel gère ça)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zotero_items');
    }
};