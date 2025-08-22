<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GlobalRegion; // Continuez à utiliser ce modèle, il est juste mappé à ego_observatory

class GlobalRegionController extends Controller
{
    public function index()
    {
        // Objectif: Récupérer toutes les entrées de 'ego_observatory' qui représentent
        // des zones géographiques (polygones) et qui ont des coordonnées valides.
        // Nous allons exclure les entrées dont le 'type' est 'reg_obs' car elles
        // sont utilisées comme catégories/groupes et non comme des zones à dessiner.

        $regionsForMap = GlobalRegion::where('type', '!=', 'reg_obs') // Exclure les catégories
                                    ->whereNotNull('latmin') // S'assurer que les coordonnées ne sont pas nulles
                                    ->whereNotNull('latmax')
                                    ->whereNotNull('lonmin')
                                    ->whereNotNull('lonmax')
                                    ->get(); // Récupère une collection, qui sera sérialisée en JSON Array

        // Retourne la collection de régions directement.
        // Laravel sérialisera automatiquement cette collection en un tableau JSON.
        return response()->json($regionsForMap);
    }
}