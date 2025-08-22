<?php

namespace App\Http\Controllers;

// use App\Models\EgoGroup;
use App\Models\EgoMember;
// use App\Models\EgoUser;
// use App\Models\EgoUserTest;
use App\Models\User;
// use App\Models\EgoUserGroupRequest;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class RegisterFormulaireController extends Controller
{
    
    

    
    public function genererFormulaireHtmlV3()
    {
        // Récupérer l'email depuis la requête
        $email =  request()->query('email'); 

        // Lire le contenu JSON du fichier dans storage/app/
        $json = file_get_contents(resource_path('DescriptionFormulaireRegister.json'));

        $champs = json_decode($json, true);

        foreach ($champs as &$champ) {
            if ($champ['name'] === 'userMail') {
                $champ['value'] = $email;  // injecter l'email reçu
                // éventuellement tu peux ajouter une clé pour dire qu'il est readonly
                $champ['readonly'] = true;
            }
        }
        unset($champ);
        // $groupes = EgoGroup::select('group_id', 'group_name')->get();
        $groupes = EgoMember::select('item_id', 'name')->get();
        return view('dynamic-form-fuse', compact('champs', 'groupes'));
    }

    
    public function traiterFormulaireAjax(Request $request)
    {
        // Traitement du formulaire ici
        // Validation des données du formulaire
        $validated = $request;
        // if(!filter_var($validated->input('userMail'), FILTER_VALIDATE_EMAIL)) {
        //     return response()->json(['message' => 'Error, you have incorrectly filled in the email field.']);
        // }

        if(!filter_var($validated->input('professionalEmail'), FILTER_VALIDATE_EMAIL)) {
            return response()->json(['message' => 'Error, you have incorrectly filled in the professional email field.']);
        }
        // if($validated->input('userMail') !== $validated->input('userMailConfirm')){
        //     return response()->json(['message' => 'Error, you entered two different email addresses for the personal email.']);
        // }
        // if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,30}$/', $validated->input('userPassword'))) {
        //     return response()->json(['message' => 'Error, you entered a password that does not meet the requirements.']);
        // }
        // if($validated->input('userPassword') !== $validated->input('userPasswordConfirm')){
        //     return response()->json(['message' => 'Error, you entered two different passwords.']);
        // }
        if(!preg_match('/^[a-zA-Z0-9]{3,20}$/', $validated->input('username'))) {
            return response()->json(['message' => 'Error, you incorrectly filled in the username field.']);
        }
        $alreadyExists = User::where('username', $validated->input('username'))->exists();
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


        
        $user = User::where('email', $validated->input('userMail'))->first();

        if (!$user) {
            return response()->json(['message' => 'Error, no user found with that email.']);
        }



        $ego_member_id = $validated->filled('group_id') ? $validated->input('group_id') : null;

        // Enregistrement de l'utilisateur dans la base de données
        $user->update([
            'username' => $validated->input('username'),
            'userInstitute' => $validated->input('userInstitute'),
            'userInstituteWebsite' => $validated->input('userInstituteWebsite'),
            'orcid' => $validated->input('userORCID'),
            // 'userPassword' => $password,
            'first_name' => $validated->input('userFirstName'),
            'last_name' => $validated->input('userLastName'),
            'status' => 'pending',
            'professional_email' => $validated->input('professionalEmail'),
            // 'userMail' => $validated->input('userMail'),
            'motivation' => $request->input('userMotivation'),
            'ego_membership' => $request->input('ego_membership'),
            'userInstituteId' => $validated->input('group_id')
        ]);
        // Send email to admin for approval
        // TODO Faire une fonction pour envoyer un email aux admin
        // Possibilité d'accepter ou de refuser la demande via mail? 
        // Renvoiyer une réponse JSON de succès
        return response()->json(['success' => true]);
    }
      
}
