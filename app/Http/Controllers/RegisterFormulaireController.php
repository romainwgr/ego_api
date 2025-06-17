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
    /*public function formulaire(Request $request)
    {
        if ($request->isMethod('post')) {
            return $this->traiterFormulaire($request);
        }

        // Formulaire HTML retourné depuis Laravel
        return response($this->genererFormulaireHtml());
    }*/
//action="http://localhost/wordpress/formulaire-recu/"
    public function genererFormulaireHtml()
    {
        $csrf = csrf_field();
        $action = route('formulaire');

        return <<<HTML
            <form action="http://localhost/wordpress/formulaire-recu/" id="ego-inscription-form" method="post" style="margin:100px auto;width:25rem;padding:10px;border:1px solid black;border-radius:10px;background-color:#4C9A7C;">
                $csrf
                <section>
                    <label for="username">username :</label>
                    <input type="text" id="username" name="username" required>
                </section>
                <section>
                    <label for="userMail">Email :</label>
                    <input type="email" id="userMail" name="userMail" required>
                </section>
                <section>
                    <label for="userMailConfirm">Email confirm :</label>
                    <input type="email" id="userMailConfirm" name="userMailConfirm" required>
                </section>
                <section>
                    <label for="userPassword">Password :</label>
                    <input type="password" id="userPassword" name="userPassword" required>
                </section>
                <section>
                    <label for="userPasswordConfirm">Password confirm:</label>
                    <input type="password" id="userPasswordConfirm" name="userPasswordConfirm" required>
                </section>
                <section>
                    <label for="userLastName">Last name :</label>
                    <input type="text" id="userLastName" name="userLastName" required>
                </section>
                <section>
                    <label for="userFirstName">First name :</label>
                    <input type="text" id="userFirstName" name="userFirstName" required>
                </section>
                <section>
                    <label for="professionalEmail">Professional email :</label>
                    <input type="email" id="professionalEmail" name="professionalEmail" required>
                </section>
                <section>
                    <label for="userInstitute">Institute name :</label>
                    <input type="text" id="userInstitute" name="userInstitute" required>
                </section>
                <section>
                    <label for="userInstituteWebsite">Website of the institute:</label>
                    <input type="text" id="userInstituteWebsite" name="userInstituteWebsite" required>
                </section>
                <section>
                    <label for="userORCID">ORCID :</label>
                    <input type="text" id="userORCID" name="userORCID" value="0000-0000-0000-0000" required>
                </section>
                <section>
                    <label for="egoMembership">EGO membership :</label>
                    <input type = "checkbox" name = "egoMembership" value = 1>
                <section>
                    <label for="userMotivation">Motivation: <span class="required">*</span></label>
                    <textarea id="userMotivation" name="userMotivation" required></textarea>
                </section>
                <section>
                    <button type="submit">Envoyer</button>
                </section>
            </form>
            <div id="ego-form-message" style="color: red; margin-top: 10px;"></div>
            <script>
                document.getElementById('ego-inscription-form').addEventListener('submit', function(e) {
                    e.preventDefault();
                    const form = e.target;
                    const formData = new FormData(form);
                    formData.append('action', 'ego_envoyer_inscription');

                    fetch('http://localhost:8000/api/inscription', {
                        method: 'POST',
                        body: formData
                    })
                    .then(async res => {
                        const data = await res.json();
                        const messageDiv = document.getElementById('ego-form-message');
                        messageDiv.textContent = ""; // Réinitialiser le message avant d'afficher un nouveau
                        if (!res.ok) {
                            // Laravel retourne souvent une structure de ce type en cas d'erreur de validation
                            const errors = data.errors;
                            if (errors) {
                                // On récupère le premier message d'erreur
                                const firstError = Object.values(errors)[0][0];
                                messageDiv.textContent = firstError;
                            } else {
                                messageDiv.textContent = data.message || "Une erreur inconnue s'est produite.";
                            }
                            throw new Error("Erreur de validation");
                        }

                        if (data.success) {
                            form.outerHTML = `<p style="color: green;">Your registration request has been submitted successfully.</p>`;
                        } else {
                            messageDiv.textContent = data.message || "Une erreur s'est produite.";
                        }
                    })
                    .catch(error => {
                        if (messageDiv.textContent === "") {
                            messageDiv.textContent = "Erreur de communication avec le serveur.";
                        }
                        console.error("Erreur : ", error);
                    });
                    /*.then(res => res.json())
                    .then(data => {
                        const messageDiv = document.getElementById('ego-form-message');
                        console.log('envoyé');
                        if (data.success) {
                            form.outerHTML = `<p style="color: green;">Demande d'inscription réussis</p>`;
                        } else {
                            messageDiv.textContent = data.data.message || "Une erreur s'est produite.";
                        }
                    })
                    .catch(() => {
                        document.getElementById('ego-form-message').textContent = "Erreur de communication avec le serveur.";
                    });*/
                });
                </script>
        HTML;
    }

    public function genererFormulaireHtmlV2()
    {
        /*$champs = [
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
        ];*/

        
        

        // Lire le contenu JSON du fichier dans storage/app/
        $json = file_get_contents(resource_path('DescriptionFormulaire.json'));
        
        // Le décoder en tableau PHP
        $champs = json_decode($json, true);
        $groupes = EgoGroup::select('group_id', 'group_name')->get();
        return view('dynamic-form', compact('champs', 'groupes'));
    }

    public function genererFormulaireHtmlV3()
    {
        // Lire le contenu JSON du fichier dans storage/app/
        $json = file_get_contents(resource_path('DescriptionFormulaire.json'));
        
        // Le décoder en tableau PHP
        $champs = json_decode($json, true);
        $groupes = EgoGroup::select('group_id', 'group_name')->get();
        return view('dynamic-form-fuse', compact('champs', 'groupes'));
    }

    public function traiterFormulaire(Request $request)
    {
        
        /*$validated = $request->validate([
            'userLastName' => 'required|string|max:50',
            'username' => 'required|string|max:20|min:6',
            'userPassword' => 'required|string|min:6|max:30',
            'professionalEmail' => 'required|email',
            'userInstituteName' => 'required|string|max:50',
            'userInstituteWebsite' => 'required|url',
            'userORCID' => 'required|string|max:19',
            'userFirstName' => 'required|string|max:50',
            'userMail' => 'required|email',
            'userMotivation' => 'required|string|min:50',
            'userMailConfirm' => 'required|email',
            'userPasswordConfirm' => 'required|string|min:6|max:30'
        ]);
        if ($request->input('userMail') !== $request->input('userMailConfirm')) {
            return response()->json(['error' => 'Les adresses e-mail ne correspondent pas.'], Response::HTTP_BAD_REQUEST);
        }
        if ($request->input('userPassword') !== $request->input('userPasswordConfirm')) {
            return response()->json(['error' => 'Les mots de passe ne correspondent pas.'], Response::HTTP_BAD_REQUEST);
        }*/
        // Validation des données du formulaire
        $validated = $request;
        if($validated->input('userMail') !== $validated->input('userMailConfirm') || !filter_var($validated->input('userMail'), FILTER_VALIDATE_EMAIL) || !filter_var($validated->input('professionalEmail'), FILTER_VALIDATE_EMAIL)) {
            $html = $this->genererFormulaireHtml();
            $html = "<p style=' color : red;'>Erreur, vous avez mal rempli un ou plusieurs champ d'email</p>";
            return response($html);
        }
        if($validated->input('userPassword') !== $validated->input('userPasswordConfirm') || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,30}$/', $validated['userPassword'])) {
            $html = $this->genererFormulaireHtml();
            $html .= '<p style=" color : red;">Erreur, vous avez mal rempli un ou plusieurs champ du mot de passe</p>';
            return response($html);
        }
        if(!preg_match('/^[a-zA-Z0-9]{3,20}$/', $validated['username'])) {
            $html = $this->genererFormulaireHtml();
            $html = "<p style=' color : red;'>Erreur, vous avez mal rempli le champ du nom d'utilisateur</p>";
            return response($html);
        }
        if(!filter_var($validated->input('userInstituteWebsite'), FILTER_VALIDATE_URL)){
            $html = $this->genererFormulaireHtml();
            $html .= "<p style=' color : red;'>Erreur, vous avez mal rempli le champ de l'URL de l'institut</p>";
            return response($html);
        }
        if(!preg_match('/^[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{4}$/', $validated['userORCID'])) {
            $html = $this->genererFormulaireHtml();
            $html .= "<p style=' color : red;'>Erreur, vous avez le champ de l'ORCID</p>";
            return response($html);
        }
        if(empty($validated['userLastName']) || empty($validated['userFirstName']) || empty($validated['userInstitute'])) {
            $html = $this->genererFormulaireHtml();
            $html .= '<p style=" color : red;">Erreur, vous avez mal rempli un ou plusieurs champ</p>';
            return response($html);
        }
        if(strlen($validated['userMotivation']) < 50) {
            $html = $this->genererFormulaireHtml();
            $html .= '<p style=" color : red;">Erreur, vous avez mal rempli le champ de la motivation</p>';
            return response($html);
        }

        // Hachage du mot de passe
        $algo = config('hashing.driver');
        $validated['password'] = Hash::driver($algo)->make($validated['password']);
                   
        // Enregistrement de l'utilisateur dans la base de données
        $egoUserTest = EgoUserTest::create([
            'username' => $validated['username'],
            'userInstitute' => $validated['userInstitute'],
            'userInstituteWebsite' => $validated['userInstituteWebsite'],
            'userORCID' => $validated['userORCID'],
            'userPassword' => $validated['userPassword'],
            'userFirstName' => $validated['userFirstName'],
            'userLastName' => $validated['userLastName'],
            'status' => 'pending',
            'professionalEmail' => $validated['professionalEmail'],
            'userMail' => $validated['userMail'],
            'userMotivation' => $request->input('userMotivation'),
            'egoMembership' => $request->input('egoMembership')
        ]);

        // Traitement des données ici

        $html = "<h2>Merci pour votre soumission !</h2>";
        $html .= "<p>Nom : {$validated['userLastName']}</p>";
        $html .= "<p>Prénom : {$validated['userFirstName']}</p>";
        $html .= "<p>Email : {$validated['userMail']}</p>";
        $html .= '<a href="http://localhost/wordpress/32-2/" >Revenir au formulaire</a>';

        return response($html);
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
    public function traiterFormulaireAjaxV2(Request $request)
    {
        // Traitement du formulaire ici
        // Validation des données du formulaire
        $validated = $request;

        $json = file_get_contents(resource_path('DescriptionFormulaireRules.json'));
        $champs = json_decode($json, true);
        
        foreach($champs as $champ) {
            if($champ['ruleType'] == 'exist') {  
                if(empty($validated->input($champ['name']))) {
                    return response()->json(['message' => $champ['message']]);
                }
            }
            else if($champ['ruleType'] == 'filter') {
                if($champ['rule'] == 'email') {
                    if(!filter_var($validated->input($champ['name']), FILTER_VALIDATE_EMAIL)) {
                        return response()->json(['message' => $champ['message']]);
                    }
                }
                else if($champ['rule'] == 'url') {
                    if(!filter_var($validated->input($champ['name']), FILTER_VALIDATE_URL)) {
                        return response()->json(['message' => $champ['message']]);
                    }
                }
            }
            else if($champ['ruleType'] == 'regex') {
                if(!preg_match($champ['rule'], $validated->input($champ['name']))) {
                    return response()->json(['message' => $champ['message']]);
                }
            }
            else if($champ['ruleType'] == 'length') {
                if(strlen($validated->input($champ['name'])) < $champ['min']) {
                    return response()->json(['message' => $champ['message']]);
                }
            }
            else if($champ['ruleType'] == 'same') {
                if($validated->input($champ['name']) !== $validated->input($champ['rule'])) {
                    return response()->json(['message' => $champ['message']]);
                }
            }
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
            return response()->json(['success' => true]);
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
        return response()->json(['success' => true]);
    }   
}
