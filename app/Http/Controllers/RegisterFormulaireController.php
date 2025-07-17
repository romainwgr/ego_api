<?php

namespace App\Http\Controllers;

use App\Models\EgoGroup;
use App\Models\EgoUser;
use App\Models\EgoUserTest;
use App\Models\EgoUserGroupRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class RegisterFormulaireController extends Controller
{
    
    

    /*public function genererFormulaireHtmlV2()
    {
        // Lire le contenu JSON du fichier dans storage/app/
        $json = file_get_contents(resource_path('DescriptionFormulaire.json'));
        
        // Le décoder en tableau PHP
        $champs = json_decode($json, true);
        $groupes = EgoGroup::select('group_id', 'group_name')->get();
        return view('dynamic-form', compact('champs', 'groupes'));
    }*/

    public function genererFormulaireHtmlV3()
    {
        // Lire le contenu JSON du fichier dans storage/app/
        $json = file_get_contents(resource_path('DescriptionFormulaireRegister.json'));
        
        // Le décoder en tableau PHP
        $champs = json_decode($json, true);
        $groupes = EgoGroup::select('group_id', 'group_name')->get();
        return view('dynamic-form-fuse', compact('champs', 'groupes'));
    }

    
    public function traiterFormulaireAjax(Request $request)
    {
        // Traitement du formulaire ici
        // Validation des données du formulaire
        $validated = $request;
        if(!filter_var($validated->input('userMail'), FILTER_VALIDATE_EMAIL)) {
            return response()->json(['message' => 'Error, you have incorrectly filled in the email field.']);
        }
        if(!filter_var($validated->input('professionalEmail'), FILTER_VALIDATE_EMAIL)) {
            return response()->json(['message' => 'Error, you have incorrectly filled in the professional email field.']);
        }
        if($validated->input('userMail') !== $validated->input('userMailConfirm')){
            return response()->json(['message' => 'Error, you entered two different email addresses for the personal email.']);
        }
        if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,30}$/', $validated->input('userPassword'))) {
            return response()->json(['message' => 'Error, you entered a password that does not meet the requirements.']);
        }
        if($validated->input('userPassword') !== $validated->input('userPasswordConfirm')){
            return response()->json(['message' => 'Error, you entered two different passwords.']);
        }
        if(!preg_match('/^[a-zA-Z0-9]{3,20}$/', $validated->input('username'))) {
            return response()->json(['message' => 'Error, you incorrectly filled in the username field.']);
        }
        $alreadyExists = EgoUserTest::where('username', $validated->input('username'))->exists();
        if($alreadyExists) {
            return response()->json(['message' => 'Error, the username already exists.']);
        }
        if(!filter_var($validated->input('userInstituteWebsite'), FILTER_VALIDATE_URL)){
            return response()->json(['message' => 'Error, you incorrectly filled in the institute URL field.']);
        }
        if(!empty($validated->input('userORCID') && !preg_match('/^[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{4}$/', $validated->input('userORCID')))) {
            return response()->json(['message' => 'Error, you incorrectly filled in the ORCID field.']);
        }
        if(empty($validated->input('userLastName'))) {
            return response()->json(['message' => 'Error, you did not fill in the last name field.']);
        }
        if(empty($validated->input('userInstitute'))){
            return response()->json(['message' => 'Error, you did not fill in the institute name field.']);
        }
        if(empty($validated->input('userFirstName'))){
            return response()->json(['message' => 'Error, you did not fill in the first name field.']);
        }
        if(strlen($validated->input('userMotivation')) < 50) {
            return response()->json(['message' => 'Error, you incorrectly filled in the motivation field.']);
        }

        
        // Hachage du mot de passe
        $algo = config('hashing.driver');
        $password = Hash::driver($algo)->make($validated->input('password'));

        // Enregistrement de l'utilisateur dans la base de données
        $egoUserTest = EgoUserTest::create([
            'username' => $validated->input('username'),
            'userInstitute' => $validated->input('userInstitute'),
            'userInstituteWebsite' => $validated->input('userInstituteWebsite'),
            'userORCID' => $validated->input('userORCID'),
            'userPassword' => $password,
            'userFirstName' => $validated->input('userFirstName'),
            'userLastName' => $validated->input('userLastName'),
            'status' => 'pending',
            'professionalEmail' => $validated->input('professionalEmail'),
            'userMail' => $validated->input('userMail'),
            'userMotivation' => $request->input('userMotivation'),
            'egoMembership' => $request->input('egoMembership')
        ]);

        if(!$validated->input('group_id')){
            return response()->json(['success' => true, 'type' => 'without_group']);
        }

        //Récupération de l'id du nouvel utilisateur
        $userId = EgoUserTest::where('username', $validated->input('username'))->pluck('userId')->first();

        if(!$userId){
            return response()->json(['message' => 'Error, the user ID was not found.']);
        }
        // Enregistrement de la requete de rejoindre le groupe
        $egoUserGroupRequest = EgoUserGroupRequest::create([
            'userId' => $userId,
            'group_id' => $validated['group_id'],
            'is_displayed' => '0'
        ]);

        // Renvoiyer une réponse JSON de succès
        return response()->json(['success' => true, 'type' => 'with_group']);
    }
      
}
