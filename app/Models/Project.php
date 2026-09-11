<?php

namespace App\Models;

readonly class Project
{
    public function __construct(
        private string  $id,
        private string  $name,
        private string  $slug,
        private ?string $summary,
        private ?string $iconUrl,
        private ?string $author,
        private int     $downloads,
        private array   $categories,
        private array   $versions,
        private array   $loaders,
        private array   $projectTypes,
        private array   $sources,
    )
    {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function summary(): ?string
    {
        return $this->summary;
    }

    public function iconUrl(): ?string
    {
        return $this->iconUrl;
    }

    public function author(): ?string
    {
        return $this->author;
    }

    public function downloads(): int
    {
        return $this->downloads;
    }

    public function categories(): array
    {
        return $this->categories;
    }

    public function versions(): array
    {
        return $this->versions;
    }

    public function loaders(): array
    {
        return $this->loaders;
    }

    public function projectTypes(): array
    {
        return $this->projectTypes;
    }

    public function sources(): array
    {
        return $this->sources;
    }

    public function hasSource(string $platform): bool
    {
        foreach ($this->sources as $source) {
            if (($source['platform'] ?? null) === $platform) {
                return true;
            }
        }

        return false;
    }

    public function source(string $platform): ?array
    {
        foreach ($this->sources as $source) {
            if (($source['platform'] ?? null) === $platform) {
                return $source;
            }
        }

        return null;
    }
}
