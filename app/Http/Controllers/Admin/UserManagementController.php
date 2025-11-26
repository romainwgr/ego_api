<?php
namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
//ContactUserMail
use App\Mail\ContactUserMail;
USE Illuminate\Support\Facades\Mail;
// TODO Gérer la pagination (optionnel)
// TODO Recherche selon le mail, username, nom, prénom, institut, etc.
// TODO Trier selon le statut, le role, l'institut etc.
// TODO Envoyer des emails automatisés
// TODO Ajouter des fonctionnalités groupées pour les utilisateurs (par exemple, validation en masse, bannissement en masse, etc.)
// TODO Ajouter des fonctionnalités avancées sur les statisiques des utilisateurs (nombre d'utilisateurs, nombre de demandes en attente, etc.)
// TODO Modifier les rôles des utilisateurs (admin, user, etc.)
// TODO Bannir un utilisateur et le débannir
/**
 * UserManagementController handles user management tasks for administrators.
 */
class UserManagementController extends Controller
{
    /**
     * Display a list of pending user requests.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPendingRequests(Request $request)
    {
        $pendingRequests = User::where('status', 'pending')
            ->with(['egoMember'])
            ->get();
        $pendingRequestsTotal = $pendingRequests->count();
        return response()->json([
            'message' => 'Pending requests retrieved successfully',
            'data' => $pendingRequests,
            'total'=> $pendingRequestsTotal,
        ]);
    }
    /**
     * Approve a user request by validating the user.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveRequest(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->status = 'validated'; 
        $user->save();

        Log::info("User {$user->id} validated by admin.");
        // TODO // Send email to user about approval
        return response()->json([
            'message' => 'User request validated successfully',
            'data' => $user
        ]);
    }
    /**
     * Reject a user request.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function rejectRequest(Request $request, $id){
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->status = 'rejected'; 
        $user->save();

        Log::info("User {$user->id} rejected by admin.");

        return response()->json([
            'message' => 'User request rejected successfully',
            'data' => $user
        ]);
    }
    /**
     * Display a list of all users.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUsers(Request $request)
    {
        $users = User::with('egoMember')->get();
        $totalUsers = $users->count();

        if ($users->isEmpty()) {
            return response()->json(['message' => 'No users found'], 404);
        }

        return response()->json([
            'message' => 'All users retrieved successfully',
            'data' => $users,
            'total'=> $totalUsers,
        ]);
    }
    /**
     * Display a specific user by ID.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserById(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'message' => 'User retrieved successfully',
            'data' => $user
        ]);
    }
    
    
    /**
     * Ban a user.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function banUser(Request $request, $id)
{
    try {
        Log::info("banUser called", ['id' => $id]);

        $user = User::find($id);

        if (!$user) {
            Log::warning("User not found in banUser", ['id' => $id]);
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->status = 'banned';
        $user->save();

        Log::info("User {$user->id} banned by admin.");

        return response()->json([
            'message' => 'User banned successfully',
            'data' => $user
        ]);
    } catch (\Throwable $e) {
        Log::error('banUser failed: '.$e->getMessage(), [
            'id'    => $id,
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'message' => 'Erreur interne dans banUser',
            'error'   => $e->getMessage(),      // à désactiver en prod si besoin
        ], 500);
    }
}

    public function getBannedUsers(Request $request)
    {
        $bannedUsers = User::where('status', 'banned')           
            ->with(['egoMember'])
            ->get();

        $bannedUsersCount = $bannedUsers->count();

        return response()->json([
            'message' => 'Banned users retrieved successfully',
            'data' => $bannedUsers,
            'total' => $bannedUsersCount,
        ]);
    }
    public function unbanUser(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->status = 'validated'; 
        $user->save();

        Log::info("User {$user->id} unbanned by admin.");

        return response()->json([
            'message' => 'User unbanned successfully',
            'data' => $user
        ]);
    }
    public function getRejectedUsers(Request $request)
    {
        $rejectedUsers = User::where('status', 'rejected')
            ->with(['egoMember'])
            ->get();

        $rejectedUsersTotal = $rejectedUsers->count();
        //gestion de la liste vide dans le frontend
        return response()->json([
            'message' => 'Rejected users retrieved successfully',
            'data' => $rejectedUsers,
            'total' => $rejectedUsersTotal,
        ]);
    }
    // Récupère les utilisateurs étant dans le même institut que l'utilisateur connecté
    public function getMyInstituteUsers(Request $request){
        $user = $request->get('auth_user');
        $myInstitute = $user->ego_member_id;
        $myInstituteUsersWithoutMe = User::where('ego_member_id', $myInstitute)
            ->where('id', '!=', $user->id)
            ->with(['egoMember'])
            ->get();
        $myInstituteUsersWithoutMeTotal = $myInstituteUsersWithoutMe->count();
        return response()->json([
            'message' => 'Users from my institute retrieved successfully',
            'data'=> $myInstituteUsersWithoutMe,
            'total' => $myInstituteUsersWithoutMeTotal
        ]);
    }
    public function getUncompletedUsers(Request $request){
        
        $uncompletedUsers = User::where('status', 'uncompleted')->get();
        $uncompletedUsersTotal = $uncompletedUsers->count();
        return response()->json([
            'message' => 'Uncompleted users retrieved successfully',
            'data' => $uncompletedUsers,
            'total' => $uncompletedUsersTotal,
        ]);
    }
    public function contactUser(Request $request, $id){
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        Mail::to($user->email)->send(
            new ContactUserMail(
                $request->input('message'),
                $request->input('subject')
            )
        );

        return response()->json(['message' => 'Email sent successfully']);
    }

}