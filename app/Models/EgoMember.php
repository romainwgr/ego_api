<?php
//attached_icon,name,name_detail,resp_phpbbid,address,locator,edmoRecordId,country where is_display = 1

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EgoMember extends Model
{
    protected $table = 'ego_members';
    protected $primaryKey = 'item_id';

    public $timestamps = false;

    
    protected $fillable = [
        // 'item_id',
        'attached_icon',
        'lat',  
        'lon',
        'name',
        'name_detail',
        'resp_phpbbid',
        'address',
        'locator',
        'edmoRecordId',
        'resp_inclear',
        'country',
        'is_displayed',
        'request_status',
        'locatorInstitute', 
        'gtt_members',      
        'tech_responsible', 
        'gliders',          
        'asvs',
    ];
    protected $casts = [
        'resp_phpbbid' => 'integer',
        'edmoRecordId' => 'integer',
        'is_displayed' => 'boolean',
        'lat' => 'float', 
        'lon' => 'float',
    ];

    /** Relation : un ego_member possède 0..∞ users */
    public function users()
    {
        return $this->hasMany(User::class, 'ego_member_id', 'item_id');
    }

    /**
     * Scope : sélection « carte » rapide
     * (attached_icon,name,name_detail,resp_phpbbid,address,locator,edmoRecordId,country)
     */
    public function scopeCard($query)
    {
        return $query->select([
            'item_id',
            'attached_icon',
            'name',
            'name_detail',
            'resp_phpbbid',
            'address',
            'locator',
            'edmoRecordId',
            'country',
            'lat', 
            'lon',
            'locatorInstitute',
            'gtt_members',
            'tech_responsible',
            'gliders',
            'asvs',
        ])->where('is_displayed', 1)             
          ->where('request_status', 'approved'); 
    }
}