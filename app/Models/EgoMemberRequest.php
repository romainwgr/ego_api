<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class EgoMemberRequest extends Model
{
    protected $table = 'ego_member_requests';

    protected $fillable = [
        'user_id',
        'ego_member_id',
        'organization_name',
        'website',
        'status',
        'handled_by',
        'handled_at',
    ];
}
