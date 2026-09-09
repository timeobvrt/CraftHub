<?php

namespace App\Http\Controllers;

use App\Services\ModrinthService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __invoke(Request $request, ModrinthService $modrinth): mixed
    {
        $validated = $request->validate([
            "q" => ["nullable", "string", "max:255"],
            "version" => ["nullable", "string", "max:50"],
            "loader" => ["nullable", "string", "max:50"],
            "type" => ["nullable", "string", "max:50"],
        ]);

        $query = trim($validated["q"] ?? "");

        if ($query === "") {
            return view("search", [
                "projects" => [],
                "total" => 0,
                "query" => "",
            ]);
        }

        $result = $modrinth->search(
            query: $query,
            version: $validated["version"] ?? null,
            loader: $validated["loader"] ?? null,
            type: $validated["type"] ?? null,
        );

        return view("search", [
            "projects" => $result["hits"] ?? [],
            "total" => $result["total_hits"] ?? 0,
            "query" => $query,
        ]);
    }
}
