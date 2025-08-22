<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EgoGroup;

class EgoGroupController extends Controller
{
    public function search(Request $request)
    {
        $term = $request->query('q');

        // Recherche "floue" simple avec %LIKE% (remplacer si vous avez un moteur + avancé)
        $groups = EgoGroup::whereRaw('LOWER(group_name) LIKE ?', ['%' . strtolower($term) . '%'])
            ->orWhereRaw('LOWER(group_desc) LIKE ?', ['%' . strtolower($term) . '%'])
            ->limit(10)
            ->get(['group_id', 'group_name', 'group_desc']);

        return response()->json($groups);
    }
}