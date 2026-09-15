<?php

namespace App\DTO;

use App\Models\Project;

final readonly class ProjectDetails
{

    public function __construct(
        public Project $project,
        public ?string $description = null,
        public ?string $publishedAt = null,
        public ?string $updatedAt = null,
        public ?string $license = null,
        public ?string $environment = null,
        public array   $gallery = [],
        public array   $versions = [],
        public array   $links = [],
        public ?string $downloadUrl = null,
        public array   $downloads = [],
        public array   $metadata = [],
        public int     $likes = 0,
    )
    {
    }

}
