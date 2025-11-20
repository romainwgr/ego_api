<?php 
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\EgoMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class MyProfileController extends Controller
{
    public function index(Request $request)
{
    $user = $request->user(); 

    $user->loadMissing(['egoMember:item_id,name']);

    $egoMemberObj = $user->egoMember;

    // 🔹 Toujours charger la liste complète
    $egoMemberChoices = EgoMember::orderBy('name')
        ->get(['item_id', 'name'])
        ->toArray();

    return response()->json([
        'success' => true,
        'user' => [
            'id'       => $user->id,
            'username' => $user->username,
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'email'    => $user->email,
            'professional_email' => $user->professional_email,
            'orcid'    => $user->orcid,
            'user_institute' => $user->userInstitute,
            'motivation' => $user->motivation,
            'role'     => $user->role,
            'status'   => $user->status,
            'ego_membership' => $user->ego_membership,

            'ego_member_id' => $user->ego_member_id,

            'ego_member' => $egoMemberObj ? [
                'item_id' => $egoMemberObj->item_id,
                'name'    => $egoMemberObj->name,
            ] : null,
            'ego_member_choices' => $egoMemberChoices,
        ],
    ]);
}


    public function updateProfile(Request $request)
    {
        
        $user = $request->user();
        // 1) Validation
        $data = $this->validateProfileData($request, $user);

        // 2) Normalisation ('' -> null, etc.)
        $data = $this->normalizeProfileData($data);

        // 3) Construction de l'array à mettre à jour
        $attributes = $this->buildProfileAttributes($data);

        // 4) Mise à jour en base
        if (! empty($attributes)) {
            $user->update($attributes);
        }

        // 5) Réponse JSON
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'user' => [
                'username'     => $user->username,
                'email'        => $user->email,
                'ego_member_id'=> $user->ego_member_id,
                'motivation'   => $user->motivation,
            ],
        ]);
    }
    protected function validateProfileData(Request $request, User $user): array
    {
        $table = $user->getTable();

        return $request->validate([
            'username' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique($table, 'username')->ignore($user->id),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique($table, 'email')->ignore($user->id),
            ],
            'ego_member_id' => [
                'nullable',
                'integer',
                'exists:ego_members,item_id',
            ],
            'motivation' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);
    }
    protected function normalizeProfileData(array $data): array
    {
        foreach (['username', 'email', 'ego_member_id', 'motivation'] as $field) {
            if (array_key_exists($field, $data) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        return $data;
    }
    protected function buildProfileAttributes(array $data): array
    {
        $fields = ['username', 'email', 'ego_member_id', 'motivation'];

        $attributes = [];

        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $attributes[$field] = $data[$field];
            }
        }

        return $attributes;
    }
    public function modifyPassword(){}
}