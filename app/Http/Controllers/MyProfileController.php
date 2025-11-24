<?php 
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\EgoMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Models\EgoMemberRequest;


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
    public function completeProfile(Request $request)
{
    $user = $request->user();

    $data = $this->validateCompleteProfileData($request, $user);

    $data = $this->normalizeCompleteProfileData($data);

    $attributes = $this->buildCompleteProfileAttributes($data);

    if (! empty($attributes)) {
        $user->update($attributes);
    }
    // 4) Gestion de l'organisation / request
    // Cas 2 : nouvelle organisation (pas d'ego_member_id)
    if (empty($data['ego_member_id'])) {
        EgoMemberRequest::create([
            'user_id'           => $user->id,
            'ego_member_id'     => null,
            'organization_name' => $data['organization_name'] ?? null,
            'website'           => $data['institute_website'] ?? null,
            'status'            => 'pending',
        ]);
    }
    if (in_array($user->status, ['uncompleted', 'rejected'], true)) {
        $user->status = 'pending';
        $user->save(); 
    }

    return response()->json([
        'success' => true,
        'message' => 'Profile completed successfully.',
        'user' => [
            'first_name'     => $user->first_name,
            'last_name'      => $user->last_name,
            'username'       => $user->username,
            'professional_email'          => $user->professional_email,
            'ego_member_id'  => $user->ego_member_id,
            'orcid'          => $user->orcid,
            'newsletter'     => (bool) $user->newsletter,
            'motivation'     => $user->motivation,
            'status'         => $user->status,
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
                'required',
                'string',
                'max:255',
                Rule::unique($table, 'username')->ignore($user->id),
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique($table, 'email')->ignore($user->id),
            ],
            'ego_member_id' => [
                'required',
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
    protected function validateCompleteProfileData(Request $request, User $user): array
{
    $table = $user->getTable();

    return $request->validate([
        'first_name' => [
            'required',
            'string',
            'max:255',
        ],
        'last_name' => [
            'required',
            'string',
            'max:255',
        ],
        'username' => [
            'required',
            'string',
            'max:255',
            Rule::unique($table, 'username')->ignore($user->id),
        ],
        
        'professional_email' => [
            'required',
            'email',
            'max:255',
            Rule::unique($table, 'professional_email')->ignore($user->id),
        ],
        'ego_member_id' => [
            'nullable',
            'integer',
            'exists:ego_members,item_id',
        ],

        // Cas nouvelle orga : nom obligatoire si pas d'ego_member_id
        'organization_name' => [
            'required_without:ego_member_id',
            'nullable',
            'string',
            'max:255',
        ],

        // Website obligatoire si organisation_name présent
        'institute_website' => [
            'required_with:organization_name',
            'nullable',
            'url',
            'max:255',
        ],
        'orcid' => [
            'nullable',
            'regex:/^\d{4}-\d{4}-\d{4}-\d{3}[\dX]$/',
        ],
        'newsletter' => [
            'nullable',
            'boolean',
        ],
        'motivation' => [
            'required',
            'string',
            'max:500',
        ],
    ]);
}
protected function normalizeCompleteProfileData(array $data): array
{
    // Champs texte qui peuvent devenir null si vides
    foreach (['first_name', 'last_name', 'username', 'professional_email', 'orcid', 'institute_website','organization_name', 'motivation'] as $field) {
        if (array_key_exists($field, $data) && $data[$field] === '') {
            $data[$field] = null;
        }
    }

    // Newsletter -> bool
    if (array_key_exists('newsletter', $data)) {
        $data['newsletter'] = filter_var($data['newsletter'], FILTER_VALIDATE_BOOLEAN);
    }

    return $data;
}
protected function buildCompleteProfileAttributes(array $data): array
{
    $fields = [
        'first_name',
        'last_name',
        'username',
        'professional_email',
        'ego_member_id',
        'orcid',
        'newsletter',
        'motivation',
    ];

    $attributes = [];

    foreach ($fields as $field) {
        if (array_key_exists($field, $data)) {
            $attributes[$field] = $data[$field];
        }
    }

    return $attributes;
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