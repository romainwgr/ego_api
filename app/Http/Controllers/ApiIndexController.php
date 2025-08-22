<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class ApiIndexController extends Controller
{
    public function index(): JsonResponse
    {
        $resources = [
         
            'members' => route('members.index'),
            'globalregions' => route('globalregions.index'),
            'globalregiontable' => route('globalregiontable.index'),
            'zotero-items' => route('zotero-items.index'),
        ];

        return response()->json([
            'message' => 'Liste des ressources disponibles',
            'resources' => $resources,
        ]);
    }
}
