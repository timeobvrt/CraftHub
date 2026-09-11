<?php

namespace App\Services;

use App\DTO\ProjectSearchQuery;
use App\Models\Project;
use Illuminate\Support\Collection;

final readonly class ProjectSearchService
{
    public function __construct(
        private ProjectProviderRegistry $providers,
    )
    {
    }

    public function search(
        string  $query,
        ?string $version = null,
        ?string $loader = null,
        ?string $type = null,
        string  $sort = 'relevance',
        int     $page = 1,
        int     $perPage = 20,
    ): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);

        $searchQuery = new ProjectSearchQuery(
            query: $query,
            version: $version,
            loader: $loader,
            type: $type,
            sort: $sort,
            page: $page,
            perPage: $perPage,
        );

        $projects = collect();
        $total = 0;
        $lastPage = 1;

        foreach ($this->providers->all() as $provider) {
            if (!$provider->supports($searchQuery)) {
                continue;
            }

            try {
                $result = $provider->search(
                    query: $searchQuery,
                    limit: $perPage,
                    page: $page,
                );
            } catch (\Throwable $exception) {
                report($exception);

                continue;
            }

            $providerProjects = $result->projects
                ->filter(fn(
                    $project
                ) => $project instanceof Project)
                ->values();

            $projects = $projects->concat($providerProjects);
            $total += max(0, $result->total);

            $providerLastPage = max(
                1, (int)ceil($result->total / $perPage)
            );

            $lastPage = max($lastPage, $providerLastPage);
        }

        $projects = $this->deduplicate($projects);
        $projects = $this->sort(
            $projects,
            $query,
            $sort,
        )->values();

        return [
            'projects' => $projects,
            'total' => $total,
            'page' => $page,
            'lastPage' => $lastPage,
            'hasMore' => $page < $lastPage,
        ];
    }

    /**
     * @param Collection<int, Project> $projects
     *
     * @return Collection<int, Project>
     */
    private function deduplicate(
        Collection $projects,
    ): Collection
    {
        $result = collect();

        foreach ($projects as $project) {
            if (!$project instanceof Project) {
                continue;
            }

            $duplicateIndex = $result->search(
                fn(
                    $existing
                ) => $existing instanceof Project
                    && $this->areDuplicates($existing, $project),
            );

            if ($duplicateIndex === false) {
                $result->push($project);

                continue;
            }

            $existing = $result->get($duplicateIndex);

            if ($existing instanceof Project) {
                $result->put(
                    $duplicateIndex,
                    $this->merge($existing, $project),
                );
            }
        }

        return $result->values();
    }

    private function areDuplicates(
        Project $a,
        Project $b,
    ): bool
    {
        $aName = $this->normalize($a->name());
        $bName = $this->normalize($b->name());

        if ($aName === '' || $bName === '') {
            return false;
        }

        if ($aName === $bName) {
            return true;
        }

        similar_text(
            $aName,
            $bName,
            $percentage,
        );

        if ($percentage < 94) {
            return false;
        }

        if ($a->author() && $b->author()) {
            return $this->normalize($a->author()) === $this->normalize($b->author());
        }

        return false;
    }

    private function normalize(
        string $value
    ): string
    {
        return preg_replace(
            '/[^a-z0-9]+/',
            '',
            strtolower($value)
        ) ?? '';
    }

    private function merge(
        Project $a,
        Project $b,
    ): Project
    {
        $primary = $a->hasSource('modrinth')
            ? $a
            : $b;

        $secondary = $primary === $a
            ? $b
            : $a;

        return new Project(
            id: $primary->id(),
            name: $primary->name(),
            slug: $primary->slug(),

            summary: $primary->summary()
            ?? $secondary->summary(),

            iconUrl: $primary->iconUrl()
            ?? $secondary->iconUrl(),

            author: $primary->author()
            ?? $secondary->author(),

            downloads: max(
                $primary->downloads(),
                $secondary->downloads(),
            ),

            categories: array_values(
                array_unique([
                    ...$primary->categories(),
                    ...$secondary->categories(),
                ]),
            ),

            versions: array_values(
                array_unique([
                    ...$primary->versions(),
                    ...$secondary->versions(),
                ]),
            ),

            loaders: array_values(
                array_unique([
                    ...$primary->loaders(),
                    ...$secondary->loaders(),
                ]),
            ),

            projectTypes: array_values(
                array_unique([
                    ...$primary->projectTypes(),
                    ...$secondary->projectTypes(),
                ]),
            ),

            sources: collect([
                ...$primary->sources(),
                ...$secondary->sources(),
            ])
                ->unique(
                    fn(
                        array $source
                    ) => $source['platform']
                        . ':'
                        . $source['id'],
                )
                ->values()
                ->all(),
        );
    }

    /**
     * @param Collection<int, Project> $projects
     *
     * @return Collection<int, Project>
     */
    private function sort(
        Collection $projects,
        string     $query,
        string     $sort,
    ): Collection
    {
        return match ($sort) {
            'downloads' => $projects
                ->sort(function (
                    Project $a,
                    Project $b
                ) {
                    $comparison = $b->downloads() <=> $a->downloads();

                    if ($comparison !== 0) {
                        return $comparison;
                    }

                    return strcasecmp(
                        $a->name(),
                        $b->name()
                    );
                })
                ->values(),

            'relevance' => $projects
                ->sort(function (
                    Project $a,
                    Project $b
                ) use
                (
                    $query
                ) {
                    $comparison = $this->relevanceScore(
                            $b,
                            $query,
                        ) <=> $this->relevanceScore(
                            $a,
                            $query,
                        );

                    if ($comparison !== 0) {
                        return $comparison;
                    }

                    $downloadComparison =
                        $b->downloads() <=> $a->downloads();

                    if ($downloadComparison !== 0) {
                        return $downloadComparison;
                    }

                    return strcasecmp(
                        $a->name(),
                        $b->name(),
                    );
                })
                ->values(),

            'newest',
            'updated',
            'follows' => $projects->values(),
            default => $projects
                ->sortByDesc(
                    fn(
                        Project $project
                    ) => $this->relevanceScore(
                        $project,
                        $query,
                    ),
                )
                ->values(),
        };
    }

    private function relevanceScore(
        Project $project,
        string  $query,
    ): float
    {
        $query = $this->normalize($query);
        $name = $this->normalize($project->name());

        $score = 0;

        if ($name === $query) {
            $score += 1000;
        } elseif (str_starts_with($name, $query)) {
            $score += 250;
        }

        $score += count($project->sources()) * 5;
        $score += log10(max($project->downloads(), 1));

        return $score;
    }

    private function fromModrinth(
        array $project
    ): Project
    {
        $id = (string)(
            $project['project_id']
            ?? $project['id']
            ?? ''
        );

        $slug = $project['slug'] ?? $id;

        return new Project(
            id: 'modrinth:' . $id,

            name: $project['title']
            ?? $project['name']
            ?? 'Unknown',

            slug: $slug,

            summary: $project['description']
            ?? $project['summary']
            ?? null,

            iconUrl: $project['icon_url']
            ?? null,

            author: $project['author']
            ?? null,

            downloads: (int)($project['downloads'] ?? 0),

            categories: $project['categories'] ?? [],

            versions: $project['game_versions']
            ?? $project['versions']
            ?? [],

            loaders: $this->extractLoaders(
                $project['categories'] ?? [],
            ),

            projectTypes: match (true) {
                !empty($project['project_types']) =>
                (array)$project['project_types'],

                !empty($project['project_type']) =>
                [(string)$project['project_type']],

                default => [],
            },

            sources: [
                [
                    'platform' => 'modrinth',
                    'id' => $id,
                    'slug' => $slug,
                    'url' =>
                        'https://modrinth.com/project/'
                        . $slug,
                ],
            ],
        );
    }

    private function extractLoaders(
        array $categories,
    ): array
    {
        $knownLoaders = [
            'forge',
            'neoforge',
            'fabric',
            'quilt',
            'paper',
            'purpur',
            'velocity',
            'bukkit',
            'spigot',
            'folia',
        ];

        return array_map('strtolower', $categories)
                |> (fn(
                    $x
                ) => array_intersect($x, $knownLoaders))
                |> array_values(...);
    }
}
