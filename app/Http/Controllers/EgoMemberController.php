<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;

class EgoMemberController extends Controller
{
    public function index()
    {
        return response()->json(Member::all());
    }
    /*
    * Fonction qui créer le tableau avec les informations sur les membres d'EGO
    *
    */
    public function getEgoMemberTable(){
        $members = EgoMember::select('nationality', 'logo_ego_member', 'name', 'edmoRecordId', 'resp_inclear', 'address')->get();

        // Renvoyer les données au format JSON.
        return response()->json([
            'success' => true,
            'data' => $members
        ]);
    }

}