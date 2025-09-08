<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EgoMember;

class EgoMemberController extends Controller
{
    public function index()
    {
        return response()->json(EgoMember::all());
    }
    /*
    * Fonction qui créer le tableau avec les informations sur les membres d'EGO
    *
    */
    public function getEgoMemberTable(){
        $members = EgoMember::select('country','attached_icon', 'name', 'edmoRecordId', 'resp_inclear', 'address')->get();

        // Renvoyer les données au format JSON.
        return response()->json([
            'success' => true,
            'data' => $members
        ]);
    }
}