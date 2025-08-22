<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


Class EgoUserTest extends Model{
    protected $table = 'ego_user_test';
    public $timestamps = false;

    protected $fillable = [
        'userId',
        'username',
        'userInstitute',
        'userInstituteWebsite',
        'userORCID',
        'userPassword',
        'userFirstName',
        'userLastName',
        'status',
        'professionalEmail',
        'userMail',
        'userMotivation',
        'egoMembership'
    ];
}