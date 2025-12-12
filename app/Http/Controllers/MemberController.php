<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;

class MemberController extends Controller
{
    public function index()
    {
        // Member::all() récupère tout sans filtre. 
        // On remplace par where(...) -> get()
        $members = Member::where('request_status', 'approved')
            ->where('is_displayed', 1)
            ->get();

        return response()->json($members);
    }
//     public function getMapData()
//     {
//           $columns = [
//             'lat',
//             'lon',
//             'name',
//             'alias_name',
//             'country',
//             'locator',
//             'locatorInstitute',
//             'gtt_members',
//             'tech_responsible',
//             'gliders',
//             'asvs',
//             'eval_lab',
//             'eval_data',
//             'eval_field',
//             'eval_fleet',
//             'eval_people'
//         ];

// .
//         $members = Member::select($columns)->get();

//         return response()->json($members);
//     }
}
