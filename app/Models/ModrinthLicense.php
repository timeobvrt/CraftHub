<?php

namespace App\Models;

class ModrinthLicense
{
    public function __construct(
        private ?string $id,
        private ?string $name,
        private ?string $url,
    ) {}

    public static function fromArray(?array $data): ?self
    {
        if (empty($data)) {
            return null;
        }

        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            url: $data['url'] ?? null
        );
    }

    public function id(): ?string
    {
        return $this->id;
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function url(): ?string
    {
        return $this->url;
    }

    public function hasUrl(): bool
    {
        return filled($this->url);
    }
}
