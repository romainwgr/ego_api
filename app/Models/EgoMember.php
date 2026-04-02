<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EgoMember extends Model
{
    protected $table = 'ego_members';
    protected $primaryKey = 'item_id';

    public $timestamps = false;

    protected $fillable = [
        // Identité
        'name',
        'name_detail',      // utilisé dans /tableau-ego et formulaire déploiement
        'alias_name',       // nom du nœud GROOM RI (frontend /api/members)
        'attached_icon',    // logo
        'address',
        'country',          // code pays (ex: FR)
        'edmoRecordId',

        // Géolocalisation
        'lat',
        'lon',

        // URLs
        'locator',          // site web / URL nœud GROOM RI
        'locatorInstitute', // organisation parente

        // Contacts
        'resp_phpbbid',     // id utilisateur responsable (ancien phpBB)
        'resp_inclear',     // contact en clair
        'gtt_members',      // contact science
        'tech_responsible', // contact technique

        // Flotte
        'gliders',
        'asvs',
        'ego_gliders_count',
        'ego_deployments_count',

        // Évaluations (barres de progression dashboard)
        'eval_lab',
        'eval_data',
        'eval_field',

        // Statut
        'is_displayed',
        'request_status',
    ];

    protected $casts = [
        'resp_phpbbid'         => 'integer',
        'edmoRecordId'         => 'integer',
        'is_displayed'         => 'boolean',
        'lat'                  => 'float',
        'lon'                  => 'float',
        'gliders'              => 'integer',
        'asvs'                 => 'integer',
        'ego_gliders_count'    => 'integer',
        'ego_deployments_count'=> 'integer',
    ];

    /** Relation : un ego_member possède 0..∞ users */
    public function users()
    {
        return $this->hasMany(User::class, 'ego_member_id', 'item_id');
    }

    /**
     * Scope : sélection carte (membres approuvés et affichés)
     */
    public function scopeCard($query)
    {
        return $query->select([
            'item_id',
            'name',
            'alias_name',
            'attached_icon',
            'address',
            'country',
            'lat',
            'lon',
            'locator',
            'locatorInstitute',
            'gtt_members',
            'tech_responsible',
            'gliders',
            'asvs',
            'ego_gliders_count',
            'ego_deployments_count',
            'eval_lab',
            'eval_data',
            'eval_field',
        ])->where('is_displayed', 1)
          ->where('request_status', 'approved');
    }
}