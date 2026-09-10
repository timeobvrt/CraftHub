<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ModrinthService
{
    private const BASE_URL = 'https://api.modrinth.com/v3';

    public function search(
        string $query,
        ?string $version = null,
        ?string $loader = null,
        ?string $type = null,
        string $sort = 'relevance',
        int $limit = 20,
        int $offset = 0,
    ): array {
        $facets = [];

        if ($version) {
            $facets[] = ["game_versions:{$version}"];
        }
        if ($loader) {
            $facets[] = ["categories:{$loader}"];
        }
        if ($type) {
            $facets[] = ["all_project_types:{$type}"];
        }

        $allowedSorts = [
            'relevance',
            'downloads',
            'follows',
            'newest',
            'updated',
        ];

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'relevance';
        }

        $parameters = [
            'query' => $query,
            'limit' => $limit,
            'offset' => $offset,
            'index' => $sort,
        ];

        if (! empty($facets)) {
            $parameters['facets'] = json_encode($facets);
        }

        $cacheKey = 'modrinth.search.' . md5(json_encode($parameters));

        return Cache::remember(
            $cacheKey,
            now()->addMinutes(5),
            function () use ($parameters) {
                $response = Http::acceptJson()
                    ->timeout(10)
                    ->get(self::BASE_URL . '/search', $parameters);

                $response->throw();

                return $response->json();
            },
        );
    }

    public function project(string $idOrSlug): array
    {
        $cacheKey = 'modrinth.project.' . $idOrSlug;

        return Cache::remember(
            $cacheKey,
            now()->addMinutes(10),
            function () use ($idOrSlug) {
                $response = Http::acceptJson()
                    ->timeout(10)
                    ->get(self::BASE_URL . '/project/' . urlencode($idOrSlug));

                $response->throw();

                return $response->json();
            },
        );
    }

    public function versions(
        string $idOrSlug,
        ?string $minecraftVersion = null,
        ?string $loader = null,
    ): array {
        $parameters = [];

        if ($minecraftVersion) {
            $parameters['game_versions'] = json_encode([$minecraftVersion]);
        }

        if ($loader) {
            $parameters['loaders'] = json_encode([$loader]);
        }

        $cacheKey =
            'modrinth.versions.' . md5($idOrSlug . json_encode($parameters));

        return Cache::remember(
            $cacheKey,
            now()->addMinutes(10),
            function () use ($idOrSlug, $parameters) {
                $response = Http::acceptJson()
                    ->timeout(10)
                    ->get(
                        self::BASE_URL .
                            '/project/' .
                            urlencode($idOrSlug) .
                            '/version',
                        $parameters,
                    );

                $response->throw();

                return $response->json();
            },
        );
    }
}
