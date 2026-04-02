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

    /**
     * Liste complète de tous les membres (tous statuts) pour l'admin.
     */
    public function getAllMembers()
    {
        $members = EgoMember::orderBy('name')->get();
        return response()->json([
            'success' => true,
            'count'   => $members->count(),
            'data'    => $members,
        ]);
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
     * Action 1 : UPDATE
     * Mise à jour complète d'un membre existant par l'admin.
     */
    public function updateInfo(Request $request)
    {
        $validated = $request->validate([
            'id'                    => 'required|exists:ego_members,item_id',
            'name'                  => 'nullable|string|max:255',
            'name_detail'           => 'nullable|string|max:255',
            'alias_name'            => 'nullable|string|max:255',
            'attached_icon'         => 'nullable|string|max:512',
            'address'               => 'nullable|string',
            'country'               => 'nullable|string|max:3',
            'edmoRecordId'          => 'nullable|integer',
            'lat'                   => 'nullable|numeric|between:-90,90',
            'lon'                   => 'nullable|numeric|between:-180,180',
            'locator'               => 'nullable|url|max:512',
            'locatorInstitute'      => 'nullable|string|max:512',
            'resp_phpbbid'          => 'nullable|integer',
            'resp_inclear'          => 'nullable|string|max:255',
            'gtt_members'           => 'nullable|string|max:255',
            'tech_responsible'      => 'nullable|string|max:255',
            'gliders'               => 'nullable|integer|min:0',
            'asvs'                  => 'nullable|integer|min:0',
            'ego_gliders_count'     => 'nullable|integer|min:0',
            'ego_deployments_count' => 'nullable|integer|min:0',
            'eval_lab'              => 'nullable|numeric|between:0,100',
            'eval_data'             => 'nullable|numeric|between:0,100',
            'eval_field'            => 'nullable|numeric|between:0,100',
            'is_displayed'          => 'nullable|boolean',
            'request_status'        => 'nullable|in:pending,approved,rejected',
        ]);

        $id = $validated['id'];
        unset($validated['id']);

        $member = EgoMember::findOrFail($id);

        // On ne met à jour que les champs explicitement envoyés (pas les null absents)
        $member->fill(array_filter($validated, fn($v) => !is_null($v)));
        $member->when_modified = now();
        $member->save();

        return response()->json(['success' => true, 'message' => 'Member updated.', 'data' => $member]);
    }

    /**
     * Créer un nouveau membre EGO (admin uniquement).
     * Le membre est directement approuvé et visible.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'name_detail'           => 'nullable|string|max:255',
            'alias_name'            => 'nullable|string|max:255',
            'attached_icon'         => 'nullable|string|max:512',
            'address'               => 'nullable|string',
            'country'               => 'required|string|max:3',
            'edmoRecordId'          => 'nullable|integer',
            'lat'                   => 'required|numeric|between:-90,90',
            'lon'                   => 'required|numeric|between:-180,180',
            'locator'               => 'nullable|url|max:512',
            'locatorInstitute'      => 'nullable|string|max:512',
            'resp_phpbbid'          => 'nullable|integer',
            'resp_inclear'          => 'nullable|string|max:255',
            'gtt_members'           => 'nullable|string|max:255',
            'tech_responsible'      => 'nullable|string|max:255',
            'gliders'               => 'nullable|integer|min:0',
            'asvs'                  => 'nullable|integer|min:0',
            'ego_gliders_count'     => 'nullable|integer|min:0',
            'ego_deployments_count' => 'nullable|integer|min:0',
            'eval_lab'              => 'nullable|numeric|between:0,100',
            'eval_data'             => 'nullable|numeric|between:0,100',
            'eval_field'            => 'nullable|numeric|between:0,100',
        ]);

        $member = EgoMember::create(array_merge($validated, [
            'is_displayed'   => true,
            'request_status' => 'approved',
            'when_created'   => now(),
            'when_modified'  => now(),
        ]));

        return response()->json(['success' => true, 'message' => 'Member created.', 'data' => $member], 201);
    }

    /**
     * Supprimer un membre EGO (admin uniquement).
     */
    public function destroy(int $id)
    {
        $member = EgoMember::findOrFail($id);
        $member->delete();

        return response()->json(['success' => true, 'message' => 'Member deleted.']);
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