<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalRegion extends Model
{
    protected $table = 'ego_observatory'; // Assurez-vous que c'est bien 'ego_observatory' maintenant
    protected $primaryKey = 'item_id'; // La clé primaire est 'item_id' dans ego_observatory
    public $timestamps = false; // Pas de colonnes created_at/updated_at par défaut

    // Vous n'avez pas besoin de mettre toutes les colonnes ici si vous ne les "fill" pas en masse
    // Mais c'est une bonne pratique de lister les attributs que vous manipulerez
    protected $fillable = [
        'item_id',
        'name',          // Correspond à region_name/short_name pour le texte affiché
        'short_name',  
        'zoneGeo',
        'latmin',
        'latmax',
        'lonmin',
        'lonmax',
        'owner_id',     
        'owner_detail', 
        'coordinator_id', 
        'type',         
        'belongto_id',   
        'attached_icon',
        'description',
        'comment',
        'access',
        'display',
        'category',
        'when_created',
        'who_created', 
        'locator',        
    ];
}