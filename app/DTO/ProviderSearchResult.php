<?php

namespace App\DTO;

use App\Models\Project;
use Illuminate\Support\Collection;

final readonly class ProviderSearchResult
{

    /**
     * @param Collection<int, Project> $projects
     */
    public function __construct(
        public Collection $projects,
        public int        $total
    )
    {
    }

    public static function empty(): self
    {
        return new self(
            projects: collect(),
            total: 0
        );
    }

}
