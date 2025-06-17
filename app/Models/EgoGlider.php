<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class EgoGlider extends Model
{
    // Define the table associated with the model
    protected $table = 'ego_glider';

    // Specify the fillable attributes
    protected $fillable = [
        'glider_id',
        'name',
        'family',
        'WMO_platform_code',
        'no_serie',
        'owner_id',
        'type'
    ];
}