<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class EgoObservatory extends Model
{
    // Define the table associated with the model
    protected $table = 'ego_observatory';

    // Specify the fillable attributes
    protected $fillable = [
        'item_id',
        'name',
        
    ];
    protected $primaryKey = 'item_id';
    public $timestamps = false;
}