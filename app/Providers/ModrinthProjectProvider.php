<?php

namespace App\Providers;

use App\Contracts\ProjectProvider;
use App\DTO\ProjectDetails;
use App\DTO\ProjectSearchQuery;
use App\DTO\ProviderSearchResult;
use App\Models\ModrinthProject;
use App\Models\Project;
use App\Services\ModrinthService;

readonly class ModrinthProjectProvider implements ProjectProvider
{
    public function __construct(
        private ModrinthService $modrinth,
    )
    {
    }

    public function key(): string
    {
        return 'modrinth';
    }

    public function supports(
        ProjectSearchQuery $query
    ): bool
    {
        return true;
    }

    public function search(
        ProjectSearchQuery $query,
        int                $limit,
        int                $page
    ): ProviderSearchResult
    {
        $offset = (max(1, $page) - 1) * $limit;
        $result = $this->modrinth->search(
            query: $query->query,
            version: $query->version,
            loader: $query->loader,
            type: $query->type,
            sort: $query->sort,
            limit: $limit,
            offset: (max(1, $page) - 1) * $limit,
        );

        $project = collect($result['hits'] ?? [])
            ->filter(fn(
                $data
            ) => is_array($data))
            ->map(fn(
                $data
            ) => $this->normalize($data))
            ->values();

        return new ProviderSearchResult(
            projects: $project,
            total: (int)($result['total_hits'] ?? 0),
        );
    }

    private function normalize(array $data): Project
    {
        $id = (string)(
            $data['project_id']
            ?? $data['id']
            ?? ''
        );

        $slug = (string)($data['slug'] ?? $id);

        $projectTypes = array_values(
            array_unique(
                (array)(
                    $data['project_types']
                    ?? $data['project_type']
                    ?? $data['all_project_types']
                ),
            ),
        );

        $categories = (array)($data['categories'] ?? []);

        $knownLoaders = [
            'forge',
            'neoforge',
            'fabric',
            'quilt',
            'paper',
            'purpur',
            'velocity',
            'bungeecord',
            'bukkit',
            'spigot',
            'folia',
        ];

        $loaders = array_map('strtolower', $categories)
                |> (fn(
                    $x
                ) => array_intersect($x, $knownLoaders))
                |> array_values(...);

        return new Project(
            id: 'modrinth:' . $id,
            name: (string)(
                $data['title']
                ?? $data['name']
                ?? 'Unknown'
            ),
            slug: $slug,
            summary: $data['summary'] ?? $data['description'] ?? null,
            iconUrl: $data['icon_url'] ?? null,
            author: $data['author'] ?? null,
            downloads: (int)($data['downloads'] ?? 0),
            categories: $categories,
            versions: (array)(
                $data['game_versions']
                ?? $data['versions']
                ?? []
            ),
            loaders: $loaders,
            projectTypes: $projectTypes,
            sources: [
                [
                    'platform' => 'modrinth',
                    'id' => $id,
                    'slug' => $slug,
                    'url' => 'https://modrinth.com/project/' . $slug,
                ],
            ],
        );
    }

    public function project(string $id): ?ProjectDetails
    {
        try {
            $data = $this->modrinth->project($id);
            $modrinthProject = new ModrinthProject($data);
            $versions = $this->modrinth->versions($id);

            $latestVersion = collect($versions)
                ->sortByDesc('date_published')
                ->first();

            $latestFile = collect($latestVersion)
                ? collect($latestVersion['files'] ?? [])
                ->firstWhere('primary', true)
                ?? collect($latestVersion['files'] ?? [])
                    ->first()
                : null;

            $downloads = collect($versions)
                ->flatMap(function (
                    array $version
                ) {
                    return collect($version['files'] ?? [])
                        ->map(function (
                            array $file
                        ) use
                        (
                            $version
                        ) {
                            $url = $file['url'] ?? null;

                            if (!$url) {
                                return null;
                            }

                            return [
                                'provider' => 'modrinth',
                                'provider_label' => 'Modrinth',
                                'name' => $version['name'] ?? $version['version_number'] ?? 'Modrinth version',
                                'version_number' => $version['version_number'] ?? null,
                                'game_versions' => array_values((array)($version['game_versions'] ?? [])),
                                'loaders' => array_values((array)($version['loaders'] ?? [])),
                                'release_type' => $version['version_type'] ?? null,
                                'published_at' => $version['date_published'] ?? null,
                                'url' => $url,
                                'filename' => $file['filename'] ?? null,
                                'primary' => (bool)($file['primary'] ?? false),
                            ];
                        });
                })
                ->filter()
                ->values()
                ->all();

            return new ProjectDetails(
                project: $this->normalize($data),
                description: $modrinthProject->description(),
                followers: $modrinthProject->followers(),
                publishedAt: $modrinthProject->publishedAt(),
                updatedAt: $modrinthProject->updatedAt(),
                license: $modrinthProject->license()?->name(),
                environment: $modrinthProject->environmentLabel(),
                gallery: $modrinthProject->gallery(),
                versions: $versions,
                links: $modrinthProject->links(),
                downloadUrl: $latestFile['url'] ?? null,
                downloads: $downloads,
                metadata: [
                    'raw' => $data,
                ],
            );
        } catch (\Throwable $throwable) {
            report($throwable);

            return null;
        }
    }
}
