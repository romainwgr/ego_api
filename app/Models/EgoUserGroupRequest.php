<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class EgoUserGroupRequest extends Model
{
    // Define the table associated with the model
    protected $table = 'ego_user_group_request';
    public $timestamps = false;
    // Specify the fillable attributes
    protected $fillable = [
        'userId',
        'group_id',
        'is_displayed'
    ];
}