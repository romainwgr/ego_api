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
        dd('Controller reached!'); 
        $members = EgoMember::select('country','attached_icon', 'name', 'edmoRecordId', 'resp_inclear', 'address')->get();

        $members->each(function ($member) {
        if ($member->attached_icon) {
            $member->attached_icon = asset('img/members/' . $member->attached_icon);
        }

        // Si le code du pays existe, construit l'URL complète du drapeau
        if ($member->country) {
            $member->country = asset('country-flags/svg/' . $member->country . '.svg');
        }
    });
        
        // Renvoyer les données au format JSON.
        return response()->json([
            'success' => true,
            'data' => $members
        ]);
    }
}