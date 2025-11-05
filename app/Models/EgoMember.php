<?php
//attached_icon,name,name_detail,resp_phpbbid,address,locator,edmoRecordId,country where is_display = 1

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


Class EgoMember extends Model{
    protected $table = 'ego_members';
    protected $primaryKey = 'item_id';

    public $timestamps = false;

    protected $fillable = [
        // 'item_id',
        'attached_icon',
        'name',
        'name_detail',
        'resp_phpbbid',
        'address',
        'locator',
        'edmoRecordId',
        'resp_inclear',
        'country',
        'is_displayed'
    ];
    protected $casts = [
        'resp_phpbbid' => 'integer',
        'edmoRecordId' => 'integer',
        'is_displayed' => 'boolean',
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
        ]);
    }
}