<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


Class EgoDeploiement extends Model{
    protected $table = 'ego_deployment';
    public $timestamps = false;

    protected $fillable = [
        'deployment_id',
        'name',
        'glider_id',
        'start_date',
        'end_date',
        'planned_start_date',
        'planned_end_date',
        'sensor_list'
    ];
}