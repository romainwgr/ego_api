<?php 
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\EgoMember;
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

        // Charge la relation si nécessaire
        $user->loadMissing(['egoMember:item_id,name']);

        // Par défaut
        $egoMemberObj   = $user->egoMember; // Model|null
        $egoMemberChoices = null;

        // Si pas d'ego_member, on propose la liste pour sélection
        if (!$egoMemberObj) {
            $egoMemberChoices = EgoMember::orderBy('name')->get(['item_id', 'name'])->toArray();
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
                

                // FK brute (int|null)
                'ego_member_id'      => $user->ego_member_id,

                // Objet du member lié (toujours le même type)
                'ego_member' => $egoMemberObj ? [
                    'item_id' => $egoMemberObj->item_id,
                    'name'    => $egoMemberObj->name,
                ] : null,

                // Liste des choix uniquement si non lié (toujours une liste ou null)
                'ego_member_choices' => $egoMemberChoices,
            ]
        ]);
    }
    public function updateOrganization(){
        // Récupère l'utilisateur connecté
        $user = $request->get('auth_user');

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'ego_member_id' => 'nullable|integer|exists:ego_members,item_id',
        ]);

        $user->update(['ego_member_id' => $validated['ego_member_id'] ?? null]);

        return response()->json([
            'success' => true,
            'message' => 'Organization updated successfully.',
        ]);
    }
    public function modifyPassword(){}
}