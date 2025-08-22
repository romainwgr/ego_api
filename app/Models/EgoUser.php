<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


Class EgoUser extends Model{
    protected $table = 'ego_user';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'phonebook_id',
        'user',
        'pass',
        'first_name',
        'last_name',
        'email',
        'mailpref',
        'disabled',
        'session_opened',
        'IDAuthor',
        'v_id_a',
        'admin',
        'hint',
        'css',
        'body_col',
        'title_col',
        'subtl_col',
        'col_1',
        'col2'
    ];
}