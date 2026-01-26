<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EgoDeploiement extends Model
{
    protected $table = 'ego_deployment';
    protected $primaryKey = 'deployment_id';
    public $timestamps = false; // Pas de created_at/updated_at

    // Relation 1 : La mission a un Glider
    public function glider()
    {
        return $this->belongsTo(EgoGlider::class, 'glider_id', 'glider_id');
    }

    // Relation 2 : La mission a un Observatoire
    public function observatory()
    {
        // Attention : la clé primaire de l'obs est 'item_id'
        return $this->belongsTo(EgoObservatory::class, 'observatory_id', 'item_id');
    }
}