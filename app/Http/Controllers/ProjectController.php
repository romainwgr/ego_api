<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;

class ProjectController extends Controller
{
    public function index()
    {
        return response()->json(Project::all());
    }



    public function getProjects(){
        $projects = Project::select('name', 'locator', 'attached_icon', 'description')->get();
        return response()->json($projects);
    }
}