<?php
//attached_icon,name,name_detail,resp_phpbbid,address,locator,edmoRecordId,country where is_display = 1

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


Class EgoMember extends Model{
    protected $table = 'ego_members';
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'attached_icon',
        'name',
        'name_detail',
        'resp_phpbbid',
        'address',
        'locator',
        'edmoRecordId',
        'country',
        'is_displayed'
    ];
}