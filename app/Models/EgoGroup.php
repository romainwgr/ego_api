<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class EgoGroup extends Model
{
    // Define the table associated with the model
    protected $table = 'myphpbb_groups';

    // Specify the fillable attributes
    protected $fillable = [
        'group_id',
        'group_name',
        'group_desc',
    ];
}