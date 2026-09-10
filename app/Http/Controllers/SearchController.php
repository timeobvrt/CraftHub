<?php

namespace App\Http\Controllers;

use App\Models\ModrinthProject;
use App\Services\ModrinthService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SearchController extends Controller
{
    public function __invoke(Request $request, ModrinthService $modrinth): mixed
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'version' => ['nullable', 'string', 'max:50'],
            'loader' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'string', 'max:50'],
            'sort' => ['nullable', Rule::in([
                'relevance',
                'downloads',
                'newest',
                'updated',
                'follows',
            ])],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $query = trim($validated['q'] ?? '');
        $page = (int) ($validated['page'] ?? 1);

        $limit = 20;
        $offset = ($page - 1) * $limit;

        $result = $modrinth->search(
            query: $query,
            version: $validated['version'] ?? null,
            loader: $validated['loader'] ?? null,
            type: $validated['type'] ?? null,
            sort: $validated['sort'] ?? 'relevance',
            limit: $limit,
            offset: $offset
        );

        $projects = collect($result['hits'] ?? [])
            ->map(fn(array $project) => new ModrinthProject($project));

        $total = $result['total_hits'] ?? 0;

        return view('search', [
            'projects' => $projects,
            'total' => $result['total_hits'] ?? 0,
            'query' => $query,
            'page' => $page,
            'lastPage' => max(1, (int) ceil($total / $limit))
        ]);
    }
}
