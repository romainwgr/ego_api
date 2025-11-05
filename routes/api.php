<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController; 
use App\Http\Controllers\Auth\SignUpController; 
use App\Http\Controllers\Auth\ResetPasswordController; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\ZoteroController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\MyProfileController;
use App\Http\Middleware\EnsureTokenIsValid;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\Auth\OidcController;
use App\Http\Controllers\EgoMemberController;
use App\Models\EgoGlider;
use App\Http\Controllers\RegisterFormulaireController;
use App\Http\Controllers\DeploiementFormulaireController;
use App\Http\Controllers\EgoMemberTableauController; 

use App\Http\Controllers\GlobalRegionController;
use App\Http\Controllers\GlobalRegionTableController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ApiIndexController;
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
    Route::middleware('auth.jwt')->get('/me', [AuthController::class, 'me']);

    Route::post('/exchange', [OidcController::class, 'exchange']);


});

// Routes utilisateur connecté (peu importe le rôle)
Route::middleware('auth.jwt')->group(function () {
    // Accède au profil de l'utilisateur connecté et renvoie ses informations 
    // ainsi que son rôle du jwt ('admin' || 'user') pour l'affichage conditionnel protégées par auth.admin
    // Pour avoir accès aux routes d'administration s'il est admin 
    Route::get('/my-profile', [MyProfileController::class, 'index']);
    Route::post('/my-profile/organization',[MyProfileController::class, 'updateOrganization'] );
});
Route::get('/embed/form/deploiement', [DeploiementFormulaireController::class, 'genererFormulaireHtml'])->middleware('allow-framing');
// Routes réservées à l'admin
Route::middleware('auth.admin')->group(function () {
    Route::get('/admin/dashboard', function () {
        return response()->json([
            'message' => 'Bienvenue Admin',
            'admin' => request()->get('auth_user')
        ]);
    });
    Route::get('/admin/pending-requests',[
        UserManagementController::class, 'getPendingRequests'
    ]);
    Route::post('/admin/approve-request/{id}', [
        UserManagementController::class, 'approveRequest'
    ]);
    Route::post('/admin/reject-request/{id}', [
        UserManagementController::class, 'rejectRequest'
    ]);
    Route::get('/admin/get-users', [
        UserManagementController::class, 'getAllUsers'
    ]);
    Route::post('/admin/get-user-info', [
        UserManagementController::class, 'getUserById'
    ]);
    Route::post('/admin/ban-user', [
        UserManagementController::class, 'banUser'
    ]);
    Route::post('/admin/unban-user', [
        UserManagementController::class, 'unbanUser'
    ]);
    Route::get('/admin/get-rejected-users',[
        UserManagementController::class,'getRejectedUsers'
    ]);
    Route::get('/admin/get-banned-users', [
        UserManagementController::class, 'getBannedUsers'
    ]);

});
    
// Zotero
Route::get('/zotero', [\App\Http\Controllers\ZoteroController::class, 'index'])->name('zotero.index');
Route::get('/publications-per-year', [\App\Http\Controllers\ZoteroController::class, 'perYear']);
Route::get('/zotero-total', [\App\Http\Controllers\ZoteroController::class, 'count']);


// Tableau ego member
Route::get('/ego-member', [\App\Http\Controllers\EgoMemberController::class, 'getEgoMemberTable']);
// New Damien

Route::get('/tableau-ego', [\App\Http\Controllers\EgoMemberTableauController::class, 'renderHtml']);

Route::post('/inscription', [RegisterFormulaireController::class, 'traiterFormulaireAjax']);
Route::get('/embed/form', [RegisterFormulaireController::class, 'genererFormulaireHtmlV3']);
// dans web.php



Route::get('/decrire-capteur/{capteur}', [DeploiementFormulaireController::class, 'popupCapteur']);

// Antoine 

Route::get('/members', [MemberController::class, 'index'])->name('members.index');


Route::get('/globalregions', [GlobalRegionController::class, 'index'])->name('globalregions.index');
Route::get('/globalregiontable', [GlobalRegionTableController::class, 'index'])->name('globalregiontable.index');
// Route::get('/zotero-items', [ZoteroController::class, 'index'])->name('zotero-items.index'); i use my zotero
Route::get('/', [ApiIndexController::class, 'index']);
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/getprojects',[ProjectController::class, 'getProjects'])->name('projects.getProjects');