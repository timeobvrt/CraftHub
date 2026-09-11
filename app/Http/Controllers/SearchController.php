<?php

namespace App\Http\Controllers;

use App\Services\ProjectSearchService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SearchController extends Controller
{
    public function __invoke(Request              $request,
                             ProjectSearchService $searchService
    ): mixed
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
        $page = max(1, (int)($validated['page'] ?? 1));

        $perPage = 20;

        $result = $searchService->search(
            query: $query,
            version: $validated['version'] ?? null,
            loader: $validated['loader'] ?? null,
            type: $validated['type'] ?? null,
            sort: $validated['sort'] ?? 'relevance',
            page: $page,
            perPage: $perPage
        );

        return view('search', [
            'projects' => $result['projects'],
            'total' => $result['total'],
            'query' => $query,
            'page' => $result['page'],
            'lastPage' => $result['lastPage'],
            'hasMore' => $result['hasMore']
        ]);
    }
}
