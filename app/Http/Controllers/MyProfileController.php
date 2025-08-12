<?php 
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class MyProfileController extends Controller
{
    public function index(Request $request)
    {
        // Récupère l'utilisateur authentifié avec le middleware auth.jwt
        $user = $request->get('auth_user');

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé dans la requête.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id'       => $user->id,
                'username' => $user->username,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email'    => $user->email,
                'professional_email' => $user->professional_email,
                'orcid'    => $user->orcid,
                'user_institute' => $user->userInstitute,
                'motivation' => $user->motivation,
                'role'     => $user->role, // 'admin' || 'user' Pour l'affichage conditionnel des routes
                'status'   => $user->status,
                'ego_membership' => $user->ego_membership,
            ]
        ]);
    }
}