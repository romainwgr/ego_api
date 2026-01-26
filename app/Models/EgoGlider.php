<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class EgoGlider extends Model
{
    protected $table = 'ego_glider';
    protected $primaryKey = 'glider_id';
    public $timestamps = false;

    public function owner()
    {
        return $this->belongsTo(EgoMember::class, 'owner_id', 'item_id');
    }
}