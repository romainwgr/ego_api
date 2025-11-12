<?php
//Pas utilisé encore
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
        'locator',//website
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