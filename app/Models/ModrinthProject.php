<?php

namespace App\Models;

class ModrinthProject
{
    public function __construct(
        private readonly array $data,
    ) {}

    public function id(): string
    {
        return $this->data['id'];
    }

    public function slug(): string
    {
        return $this->data['slug'];
    }

    public function name(): string
    {
        return $this->data['name'] ?? '';
    }

    public function summary(): string
    {
        return $this->data['summary'] ?? '';
    }

    public function description(): string
    {
        return $this->data['description'] ?? '';
    }

    public function iconUrl(): ?string
    {
        return $this->data['icon_url'] ?? null;
    }

    public function author(): string
    {
        return $this->data['author'] ?? null;
    }

    public function downloads(): int
    {
        return $this->data['downloads'] ?? 0;
    }

    public function followers(): int
    {
        return $this->data['followers'] ?? 0;
    }

    public function projectTypes(): array
    {
        return $this->data['project_types'] ?? [];
    }

    public function categories(): array
    {
        return $this->data['categories'] ?? [];
    }

    public function loaders(): array
    {
        return $this->data['loaders'] ?? [];
    }

    public function gameVersions(): array
    {
        return $this->data['game_versions'] ?? [];
    }

    public function publishedAt(): ?string
    {
        return $this->data['published'] ?? null;
    }

    public function updatedAt(): ?string
    {
        return $this->data['updated'] ?? null;
    }

    public function license(): ?ModrinthLicense
    {
        return ModrinthLicense::fromArray(
            $this->data['license'] ?? null
        );
    }

    public function raw(): array
    {
        return $this->data;
    }
}
