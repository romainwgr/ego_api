<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoteroItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'itemKey',
        'title',
        'creators', // Géré comme JSON
        'date',
        'attachment_url',
        'itemType',
        'abstractNote',
        'publicationTitle',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'creators' => 'array', // Ceci est essentiel pour que Laravel gère le champ 'creators' comme un tableau PHP <=> JSON dans la DB
    ];

    // Les propriétés ci-dessous sont les valeurs par défaut de Laravel et ne sont pas strictement nécessaires
    // mais peuvent être conservées pour la clarté si vous le souhaitez.
    // protected $table = 'zotero_items';
    // protected $primaryKey = 'id';
    // public $timestamps = true;
}