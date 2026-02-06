<?php

namespace App\Http\Controllers\Glider;

use App\Http\Controllers\Controller;  
use Illuminate\Http\Request;
use App\Models\EgoDeploiement;

class GliderController extends Controller
{
    public function index()
    {
        // 1. Récupération des données avec les relations (Optimisation SQL)
        $deployments = EgoDeploiement::with(['glider.owner', 'observatory'])
            ->orderBy('deployment_id', 'desc') // Les plus récents en premier
            ->get();

        // 2. Transformation des données pour le JSON (Nettoyage)
        $formattedData = $deployments->map(function ($mission) {
            return [
                'id'            => $mission->deployment_id,
                'glider_name'   => $mission->glider->name ?? null,
                'mission_name'  => $mission->name ?? "N/A",
                
                'lab_name'      => $mission->glider->owner->name ?? 'N/A',
                
                'observatory'   => $mission->observatory->short_name ?? 'N/A',
                'start_date'    => $mission->start_date ?? "N/A",
                'end_date'      => $mission->end_date ?? "N/A",
                'nb_dives'      => $mission->nb_dives ?? "N/A",
                'status'        => $mission->status_depl,
                'doi'           => $mission->doi,       
                'json'          => $mission->gdac_json, 
                'nc'            => $mission->gdac_nc,
            ];
        })->filter(function ($item) {
            return !empty($item['glider_name']);
        })->values();

        // 3. Retourne la réponse JSON standard
        return response()->json([
            'success' => true,
            'count'   => $formattedData->count(),
            'data'    => $formattedData
        ], 200);
    }
}