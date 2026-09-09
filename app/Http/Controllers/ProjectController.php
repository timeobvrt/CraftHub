<?php

namespace App\Http\Controllers;

use App\Services\ModrinthService;

class ProjectController extends Controller
{
    public function show(string $slug, ModrinthService $modrinth)
    {
        $project = $modrinth->project($slug);
        $version = $modrinth->versions($slug);

        return view("project", [
            "project" => $project,
            "versions" => $version,
        ]);
    }
}
