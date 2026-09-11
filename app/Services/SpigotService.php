<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SpigotService
{
    private const BASE_URL = "https://api.spiget.org/v2";

    /**
     * @return Collection<int, Project>
     */
    public function search(
        string  $query,
        ?string $type = null,
        ?string $loader = null,
        int     $limit = 20,
    ): Collection
    {
        $result = $this->searchPage(
            query: $query,
            type: $type,
            loader: $loader,
            limit: $limit,
            page: 1,
        );

        return $result['projects'];
    }

    /**
     * @return array{
     *     projects: Collection<int, Project>,
     *     total: int
     * }
     */
    public function searchPage(
        string  $query,
        ?string $type = null,
        ?string $loader = null,
        int     $limit = 10,
        int     $page = 1,
    ): array
    {
        if (
            filled($type)
            && $type !== 'plugin'
        ) {
            return [
                'projects' => collect(),
                'total' => 0,
            ];
        }

        if (
            filled($loader)
            && !in_array(
                strtolower($loader),
                [
                    'spigot',
                    'bukkit',
                    'paper',
                    'purpur',
                ],
                true,
            )
        ) {
            return [
                'projects' => collect(),
                'total' => 0,
            ];
        }

        $limit = min(max($limit, 1), 50);
        $page = max(1, $page);

        $parameters = [
            'size' => $limit,
            'page' => $page
        ];

        $cacheKey = 'spigot.search-page.v5.' . md5(
                json_encode([
                    'query' => $query,
                    'parameters' => $parameters,
                ]),
            );

        $cached = Cache::remember(
            $cacheKey,
            now()->addMinutes(5),
            function () use
            (
                $query,
                $parameters
            ) {
                $response = Http::acceptJson()
                    ->withHeaders([
                        'User-Agent' => 'CraftHub/1.0',
                    ])
                    ->timeout(10)
                    ->get(
                        self::BASE_URL
                        . '/search/resources/'
                        . urlencode($query),
                        $parameters,
                    );

                $response->throw();

                $data = $response->json();

                if (!is_array($data)) {
                    $data = [];
                }

                return [
                    'resources' => array_values(
                        array_filter(
                            $data,
                            fn(
                                $resource
                            ) => is_array($resource),
                        ),
                    ),
                    'total' => (int)(
                        $response->header('X-Total')
                        ?? count($data)
                    ),
                ];
            }
        );

        $projects = collect($cached['resources'] ?? [])
            ->filter(fn(
                $resource
            ) => is_array($resource))
            ->map(
                fn(
                    array $resource
                ) => $this->normalize($resource),
            )
            ->filter(
                fn(
                    $project
                ) => $project instanceof Project,
            )
            ->values();

        return [
            'projects' => $projects,
            'total' => (int)($cached['total'] ?? 0),
        ];
    }

    private function normalize(
        array $resource,
    ): Project
    {
        $id = (string)(
            $resource['id'] ?? ''
        );

        return new Project(
            id: 'spigot:' . $id,
            name: $resource['name'] ?? 'Unknown',
            slug: 'spigot-' . $id,
            summary: $resource['tag'] ?? null,
            iconUrl: $this->iconUrl($resource),
            author: $this->author($resource),
            downloads: (int)(
                $resource['downloads']
                ?? 0
            ),
            categories: ['spigot'],
            versions: array_values(
                array_filter(
                    (array)($resource['testedVersions'] ?? []),
                    fn(
                        $version
                    ) => is_string($version) && $version !== '',
                ),
            ),
            loaders: ['spigot'],
            projectTypes: ['plugin'],
            sources: [
                [
                    'platform' => 'spigot',
                    'id' => $id,
                    'url' => 'https://www.spigotmc.org/resources/' . $id,
                    'download_url' => self::BASE_URL . '/resources/' . $id . '/download',
                ],
            ],
        );
    }

    private function iconUrl(
        array $resource,
    ): ?string
    {
        $url = $resource['icon']['url'] ?? null;
        if (!$url) {
            return null;
        }

        if (
            str_starts_with($url, 'http://')
            || str_starts_with($url, 'https://')
        ) {
            return $url;
        }

        return
            'https://www.spigotmc.org/'
            . ltrim($url, '/');
    }

    private function author(
        array $resource
    ): ?string
    {
        $author = $resource['contributors'] ?? null;
        if (is_string($author)) {
            return $author;
        }

        if (is_array($author)) {
            return $author['name'] ?? null;
        }

        return null;
    }

    public function project(
        string $id
    ): Project
    {
        $id = preg_replace('/\D+/', '', $id) ?? '';

        abort_if($id === '', 404);

        $cacheKey = 'spigot.project.' . $id;
        $resource = Cache::remember(
            $cacheKey,
            now()->addMinutes(10),
            function () use
            (
                $id
            ) {
                return Http::acceptJson()
                    ->withHeaders([
                        'User-Agent' => 'CraftHub/1.0'
                    ])
                    ->timeout(10)
                    ->get(self::BASE_URL . '/resources/' . $id)
                    ->throw()
                    ->json();
            },
        );

        abort_unless(is_array($resource), 404);

        return $this->normalize($resource);
    }
}
