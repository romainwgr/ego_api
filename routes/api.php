<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController; 
use App\Http\Controllers\Auth\SignUpController; 
use App\Http\Controllers\Auth\ResetPasswordController; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Ici vous pouvez enregistrer les routes de votre API. Ces routes sont
| automatiquement chargées par le RouteServiceProvider dans un groupe
| qui est attribué au middleware "api".
|
*/

Route::prefix('auth')->group(function () {
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/choose-account', [LoginController::class, 'chooseAccount']);

    Route::post('/signup', [SignUpController::class, 'signup']);

    Route::post('/forgot-password', [ResetPasswordController::class, 'sendResetLink']);
    Route::post('/reset-password', [ResetPasswordController::class, 'reset']);

    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);


});

// Routes utilisateur connecté (peu importe le rôle)
Route::middleware('auth.jwt')->group(function () {
    Route::get('/profile', function () {
        return response()->json([
            'user' => request()->get('auth_user')
        ]);
    });
});

// Routes réservées à l'admin
Route::middleware('auth.admin')->group(function () {
    Route::get('/admin/dashboard', function () {
        return response()->json([
            'message' => 'Bienvenue Admin',
            'admin' => request()->get('auth_user')
        ]);
    });
});




// Damien 

// Route::get('/formulaire', [\App\Http\Controllers\RegisterFormulaireController::class, 'genererFormulaireHtml'])->name('formulaire');
/*Route::post('/formulaire', [RegisterFormulaireController::class, 'formulaire'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);*/

Route::get('/ajouter-user', function () {
    \App\Models\EgoUserTest::create([
        'username' => 'Toto',
        'userInstitute' => 'Institut de test',
        'userInstituteWebsite' => 'http://example.com',
        'userORCID' => '0000-0002-1234-5678',
        'userLastName' => 'Toto',
        'userFirstName' => 'Test',
        'userMail' => 'toto@example.com',
        'userPassword' => "yoplait17@Web",
        'userMotivation' => 'Je suis un utilisateur de test.',
        'is_validated' => 0,
        'professionalEmail' => 'maildetest@test.fr'
    ]);

    return 'Utilisateur ajouté !';
});

/*Route::post('/register', function (Request $request) {
    

    $user = \App\Models\User::create([
        'userName' => $request->userName,
        'userFirstName' => $request->userFirstName,
        'userMail' => $request->userMail,
        'userPassword' => Hash::make($request->userPassword),
    ]);
    header("Location: http://localhost/wordpress/32-2/");
    return response()->json(['success' => true, 'user' => $user]);
});*/

Route::post('/register', [\App\Http\Controllers\RegisterFormulaireController::class, 'traiterFormulaire']);

Route::get('/ego-members', function () {
    $members = \App\Models\EgoMember::select(
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
    ->get();

    return response()->json($members);
});

Route::get('/tableau-ego', [\App\Http\Controllers\EgoMemberTableauController::class, 'renderHtml']);

Route::get('/generer-json', [\App\Http\Controllers\DeploiementFormulaireController::class, 'genererJSON']);

Route::post('/inscription', [\App\Http\Controllers\RegisterFormulaireController::class, 'traiterFormulaireAjax']);

/*Route::get('/embed/form', function () {
    return view('livewire.filament.forms.user-registration-form'); // resources/views/public-form.blade.php
});*/

Route::get('/embed/form', [\App\Http\Controllers\RegisterFormulaireController::class, 'genererFormulaireHtmlV3']);
Route::get('/embed/form/old', [\App\Http\Controllers\RegisterFormulaireController::class, 'genererFormulaireHtmlV2']);
Route::get('/groups/search', [\App\Http\Controllers\EgoGroupController::class, 'search']);

Route::get('/embed/form/deploiement', [\App\Http\Controllers\DeploiementFormulaireController::class, 'genererFormulaireHtml']);
