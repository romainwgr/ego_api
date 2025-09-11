<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $table = 'ego_members';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'name',
        'latitude',
        'longitude',
        'description',
        'nationality',
        'logo_ego_member',
        'edmoRecordId',
        'resp_inclear',
        'address',
        'summary'
    ];
}