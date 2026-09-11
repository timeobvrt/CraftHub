<?php

namespace App\DTO;

final readonly class ProjectSearchQuery
{
    public function __construct(
        public string  $query,
        public ?string $version = null,
        public ?string $loader = null,
        public ?string $type = null,
        public string  $sort = 'relevance',
        public int     $page = 1,
        public int     $perPage = 20,
    )
    {
    }

    public function page(): int
    {
        return max(1, $this->page);
    }

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }
}
