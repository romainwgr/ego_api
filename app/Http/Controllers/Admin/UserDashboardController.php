<?php

namespace App\Http\Controllers\Admin;


class UserManagementController extends Controller
{
    public function getUserFromMyInstitute(Request $request)
    {
    $user = $request->get('auth_user');
    if(!$user) {
        return response()->json(['error' => 'User not authenticated'], 401);
    }
    $instituteId = $user->institute_id;
    $users = User::where('institute_id', $instituteId)->get();
        if ($users->isEmpty()) {
            return response()->json(['message' => 'No users found for this institute'], 404);
        }

        return response()->json([
            'message' => 'Users retrieved successfully',
            'data' => $users
        ]);


    
    }
    public function getInstituteUsers(Request $request)
    {
        
    }
}