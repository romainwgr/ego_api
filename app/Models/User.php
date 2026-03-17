<?php

namespace App\Models;

use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory, Notifiable, CanResetPassword;

    /**
     * Le nom de la table associée à ce modèle.
     *
     * @var string
     */
    protected $table = 'users_2025';

    /**
     * Les attributs assignables en masse.
     *
     * @var array<string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',
        'password_algo',
        'google_id',
        'status',
        'professional_email',
        'orcid',
        'userInstitute',
        'userInstituteId',
        'userInstituteWebsite',
        'motivation',
        'ego_membership',
        'ego_member_id',
        'newsletter',
    ];

    /**
     * Les attributs à cacher lors de la sérialisation.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'password_algo',
    ];

    /**
     * Les casts d’attributs.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
        /** Relation : chaque user appartient éventuellement à un ego_member */
    public function egoMember()
    {
        return $this->belongsTo(EgoMember::class, 'ego_member_id', 'item_id');
    }

    /** Scope utile : users sans ego_member */
    public function scopeWithoutEgoMember($query)
    {
        return $query->whereNull('ego_member_id');
    }
}
