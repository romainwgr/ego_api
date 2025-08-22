<?php
namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

// TODO Gérer la pagination
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

        // Assuming you have a method to get pending requests
        $pendingRequests = User::where('status', 'pending')->get();

        if ($pendingRequests->isEmpty()) {
            return response()->json(['message' => 'No pending requests found'], 404);
        }

        return response()->json([
            'message' => 'Pending requests retrieved successfully',
            'data' => $pendingRequests
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
        $users = User::all();

        if ($users->isEmpty()) {
            return response()->json(['message' => 'No users found'], 404);
        }

        return response()->json([
            'message' => 'All users retrieved successfully',
            'data' => $users
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
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->status = 'banned'; 
        $user->save();

        Log::info("User {$user->id} banned by admin.");

        return response()->json([
            'message' => 'User banned successfully',
            'data' => $user
        ]);
    }
    public function getBannedUsers(Request $request)
    {
        $bannedUsers = User::where('status', 'banned')->get();

        if ($bannedUsers->isEmpty()) {
            return response()->json(['message' => 'No banned users found',
            
        ]);
        }

        return response()->json([
            'message' => 'Banned users retrieved successfully',
            'data' => $bannedUsers
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
        $rejectedUsers = User::where('status', 'rejected')->get();

        if ($rejectedUsers->isEmpty()) {
            return response()->json(['message' => 'No rejected users found'], 404);
        }

        return response()->json([
            'message' => 'Rejected users retrieved successfully',
            'data' => $rejectedUsers
        ]);
    }

}