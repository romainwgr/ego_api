<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EgoMember;
use App\Models\EgoMemberRequest;

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
        $members = EgoMember::select('country','attached_icon', 'name', 'edmoRecordId', 'resp_inclear', 'address')
            ->where('request_status', 'approved') 
            ->where('is_displayed', 1)            
            ->get();

        $members->each(function ($member) {
            if ($member->country) {
                $code = strtolower($member->country);
                $member->country = $code;
            }
        });
        
        return response()->json([
            'success' => true,
            'data' => $members
        ]);
    }
    /**
     * Récupérer les demandes en attente (pending)
     */
    public function getEgoMemberRequests()
    {
        
        $pendingRequests = EgoMember::where('request_status', 'pending')
            ->orderBy('when_created', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'count' => $pendingRequests->count(),
            'data' => $pendingRequests
        ]);
    }

    
    
    /**
     * Action 1 : UPDATE (Brouillon)
     * On accepte tout, même si c'est vide, pour permettre à l'admin de sauvegarder son travail en cours.
     */
    public function updateInfo(Request $request)
    {
        $validated = $request->validate([
            'id'       => 'required|exists:ego_members,item_id',
            'name'     => 'nullable|string|max:128',
            'locator'  => 'nullable|url|max:256',
            'lat'      => 'nullable|numeric',
            'lon'      => 'nullable|numeric',
            'country'  => 'nullable|string|max:3',
            'address'  => 'nullable|string',
        ]);

        $member = EgoMember::findOrFail($validated['id']);
        
        // Mise à jour (on filtre les nulls pour ne pas écraser l'existant avec du vide si non envoyé)
        $member->fill(array_filter($validated, fn($value) => !is_null($value)));
        $member->when_modified = now();
        $member->save();

        return response()->json(['success' => true, 'message' => 'Data updated (Draft mode).', 'data' => $member]);
    }

    /**
     * Action 2 : APPROVE (Validation Stricte)
     * ON REFUSE si un seul champ manque.
     */
    public function approveRequest(Request $request)
    {
        // 1. On récupère les données envoyées (au cas où l'admin remplit et clique sur Approve direct)
        $request->validate([
            'id' => 'required|exists:ego_members,item_id',
            'lat' => 'nullable|numeric',
            'lon' => 'nullable|numeric',
        ]);

        $member = EgoMember::findOrFail($request->id);

        // 2. On met à jour les infos si envoyées dans la requête
        if ($request->has('name'))    $member->name = $request->name;
        if ($request->has('locator')) $member->locator = $request->locator;
        if ($request->has('lat'))     $member->lat = $request->lat;
        if ($request->has('lon'))     $member->lon = $request->lon;
        if ($request->has('country')) $member->country = $request->country;
        if ($request->has('address')) $member->address = $request->address;

        // 3. LA VÉRIFICATION STRICTE
        // On vérifie que TOUS les champs essentiels sont remplis
        $missingFields = [];
        if (empty($member->name))    $missingFields[] = 'Name';
        if (empty($member->locator)) $missingFields[] = 'Website (Locator)';
        if (empty($member->lat))     $missingFields[] = 'Latitude';
        if (empty($member->lon))     $missingFields[] = 'Longitude';
        if (empty($member->country)) $missingFields[] = 'Country';
        if (empty($member->address)) $missingFields[] = 'Address';

        if (!empty($missingFields)) {
            return response()->json([
                'success' => false, 
                'message' => 'Cannot approve: The following fields are missing: ' . implode(', ', $missingFields)
            ], 422); // Erreur 422 (Unprocessable Entity)
        }

        // 4. Si tout est bon, on valide
        $member->request_status = 'approved';
        $member->is_displayed = 1; // Visible sur la carte
        $member->when_modified = now();
        $member->save();

        return response()->json(['success' => true, 'message' => 'Institute approved and published!', 'data' => $member]);
    }

    /**
     * Action 3 : REJECT
     */
    public function rejectRequest(Request $request)
    {
        $request->validate(['id' => 'required|exists:ego_members,item_id']);

        $member = EgoMember::findOrFail($request->id);
        $member->request_status = 'rejected';
        $member->is_displayed = 0; 
        $member->when_modified = now();
        $member->save();

        return response()->json(['success' => true, 'message' => 'Request rejected.', 'data' => $member]);
    }
}