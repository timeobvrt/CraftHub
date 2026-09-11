<?php

namespace App\Contracts;

use App\DTO\ProjectDetails;
use App\DTO\ProjectSearchQuery;
use App\DTO\ProviderSearchResult;

interface ProjectProvider
{
    public function key(): string;

    public function supports(ProjectSearchQuery $query): bool;

    public function search(
        ProjectSearchQuery $query,
        int                $limit,
        int                $page
    ): ProviderSearchResult;

    public function project(string $id): ?ProjectDetails;
}
