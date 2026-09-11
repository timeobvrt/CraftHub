<?php

namespace App\Providers;

use App\Contracts\ProjectProvider;
use App\DTO\ProjectDetails;
use App\DTO\ProjectSearchQuery;
use App\DTO\ProviderSearchResult;
use App\Models\Project;
use App\Services\SpigotService;

readonly class SpigotProjectProvider implements ProjectProvider
{
    public function __construct(
        private SpigotService $spigot,
    )
    {
    }

    public function key(): string
    {
        return 'spigot';
    }

    public function supports(
        ProjectSearchQuery $query
    ): bool
    {
        if (filled($query->type) && $query->type !== 'plugin') {
            return false;
        }

        if (
            filled($query->loader)
            && !in_array(
                strtolower($query->loader),
                [
                    'spigot',
                    'bukkit',
                    'paper',
                    'purpur',
                ],
                true,
            )
        ) {
            return false;
        }

        return true;
    }

    public function search(
        ProjectSearchQuery $query,
        int                $limit,
        int                $page
    ): ProviderSearchResult
    {
        $result = $this->spigot->searchPage(
            query: $query->query,
            type: $query->type,
            loader: $query->loader,
            limit: $limit,
            page: max(1, $page),
        );

        $projects = collect($result['projects'] ?? [])
            ->filter(
                fn(
                    $project
                ) => $project instanceof Project,
            )
            ->values();

        return new ProviderSearchResult(
            projects: $projects,
            total: (int)($result['total_hits'] ?? 0),
        );
    }

    public function project(string $id): ?ProjectDetails
    {
        try {
            $project = $this->spigot->project($id);
            $source = $project->source('spigot');

            return new ProjectDetails(
                project: $project,
                description: $project->summary(),
                versions: $project->versions(),
                links: [
                    'spigot' => [
                        'label' => 'SpigotMC',
                        'url' => $source['url'] ?? null,
                    ],
                ],
                downloadUrl: $source['download_url'] ?? $source['url'] ?? null,
            );
        } catch (\Throwable $throwable) {
            report($throwable);

            return null;
        }
    }
}
