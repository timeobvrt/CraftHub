<?php

namespace App\Models;

class ModrinthGallery
{
    public function __construct(
        private ?string $url,
        private ?bool $featured,
        private ?string $title,
        private ?string $description,
        private ?string $created,
        private ?int $ordering,
    ) {}

    public static function fromArray(?array $data): ?self
    {
        if (empty($data)) {
            return null;
        }

        return new self(
            url: $data['url'] ?? null,
            featured: $data['featured'] ?? false,
            title: $data['title'] ?? null,
            description: $data['description'] ?? null,
            created: $data['created'] ?? null,
            ordering: $data['ordering'] ?? 0,
        );
    }

    public function url(): ?string
    {
        return $this->url;
    }

    public function featured(): ?bool
    {
        return $this->featured;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function title(): ?string
    {
        return $this->title;
    }

    public function created(): ?string
    {
        return $this->created;
    }


    public function ordering(): ?int
    {
        return $this->ordering;
    }
}
