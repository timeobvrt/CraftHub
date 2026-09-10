<?php

namespace App\Http\Controllers;

use App\Models\ModrinthProject;
use App\Services\ModrinthService;

class ProjectController extends Controller
{
    public function show(string $slug, ModrinthService $modrinth)
    {
        $projectData = $modrinth->project($slug);
        $project = new ModrinthProject($projectData);
        $version = $modrinth->versions($slug);

        return view("project", [
            "project" => $project,
            "versions" => $version,
        ]);
    }
}
