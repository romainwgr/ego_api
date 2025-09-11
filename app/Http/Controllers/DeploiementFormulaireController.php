<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\EgoObservatory;
use App\Models\EgoDeploiement;
// use App\Models\EgoGroup;
use App\Models\EgoGlider;
use App\Models\User;

class DeploiementFormulaireController extends Controller
{
    public function genererFormulaireHtml()
    {
        // $user = User::where('status', 'accepted')->get();
        $json = file_get_contents(resource_path('DescriptionFormulaireDeploiement.json'));
        // $gliders = EgoGlider::all();
        // Le décoder en tableau PHP
        $champs = json_decode($json, true);
        $vehicle = file_get_contents(resource_path('uuv-usv.json'));
        $vehicle = json_decode($vehicle, true);
        $json = file_get_contents(resource_path('export_sensor_model_gliders_v6.json'));
        $sensors = json_decode($json, true);
        // $groupes = EgoGroup::select('group_id', 'group_name')->get();
        $observatories = EgoObservatory::select('item_id', 'name')->get();
        $deployments = EgoDeploiement::select('deployment_id', 'name', 'glider_id', 'start_date', 'end_date', 'planned_start_date', 'planned_end_date')->get();
        $gliders = EgoGlider::select('glider_id', 'name', 'family', 'WMO_platform_code', 'no_serie', 'owner_id', 'type')->get();
        return response()->json([
            'champs'        => $champs,
            'sensors'       => $sensors,
            'vehicle'       => $vehicle,
            // 'user'          => $user,
            // 'groupes'       => $groupes,
            'observatories' => $observatories,
            'deployments'   => $deployments,
            'gliders'       => $gliders,
        ]);
        // return view('dynamic-form-deploiement', compact('champs', 'sensors', 'gliders', 'vehicle', 'user', 'groupes', 'observatories', 'deployments', 'gliders'));
        // Retourner du json et transformer dynamic form deploiement sur wordpress
        
    }
    public function traiterFormulaire(Request $request)
    {
        // traiter le formulaire

    }
    public function genererJSON()
    {
        // Générer le JSON
        /*$json = EgoMember::select(
            'attached_icon',
            'name',
            'name_detail',
            'resp_phpbbid',
            'address',
            'locator',
            'edmoRecordId',
            'country'
        )
        ->where('is_displayed', 1)
        ->orderBy('name')
        ->get()
        ->toJson(JSON_PRETTY_PRINT);*/

        $champs = [
            ['name' => 'username', 'label' => 'Username', 'type' => 'text', 'required' => true, 'subtitle'=> 'Username must be between 3 and 20 characters, and can only contain ASCII characters'],
            ['name' => 'userMail', 'label' => 'Email', 'type' => 'email', 'required' => true],
            ['name' => 'userMailConfirm', 'label' => 'Email confirm', 'type' => 'email', 'required' => true],
            ['name' => 'userPassword', 'label' => 'Password', 'type' => 'password', 'required' => true, 'subtitle'=> 'Password must be between 6 and 30 characters, and must contain at least one uppercase letter, one lowercase letter, one number, and one special character'],
            ['name' => 'userPasswordConfirm', 'label' => 'Password confirm', 'type' => 'password'],
            ['name' => 'userLastName', 'label' => 'Last Name', 'type' => 'text', 'required' => true],
            ['name' => 'userFirstName', 'label' => 'First Name', 'type' => 'text', 'required' => true],
            ['name' => 'professionalEmail', 'label' => 'Professional Email', 'type' => 'email', 'required' => true] ,
            ['name' => 'userInstitute', 'label' => 'Institute Name', 'type' => 'text', 'required' => true, 'datalist' => true],
            ['name' => 'userInstituteWebsite', 'label' => 'Website of the Institute', 'type' => 'url', 'required' => true],
            ['name' => 'userORCID', 'label' => 'ORCID', 'type' => 'text', 'value' => '0000-0000-0000-0000', 'required' => false, 'subtitle'=> 'Open Researcher and Contributor ID'],
            ['name' => 'egoMembership', 'label' => 'EGO Membership', 'type' => 'checkbox', 'required' => false],
            ['name' => 'userMotivation', 'label' => 'Motivation', 'type' => 'textarea', 'required' => true, 'subtitle'=> 'Please provide a motivation letter of at least 50 characters'],
        ];
        $json = json_encode($champs, JSON_PRETTY_PRINT);

        return response($json, 200)
        ->header('Content-Type', 'application/json')
        ->header('Content-Disposition', 'attachment; filename="utilisateur.json"');
    }
}