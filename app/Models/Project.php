<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'ego_project';
    protected $primaryKey = 'item_id';
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'phpbb_id',
        'name',
        'name_detail',
        'locator',
        'edmoRecordld',
        'resp_phpbbid',
        'date_event',
        'attached_icon',
        'description',
        'comment',
        'visibility',
        'is_displayed',
        'when_created',
        'who_created',
        'json',
    ];
}