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

    public function iconRawUrl(): ?string
    {
        return $this->data['raw_icon_url'] ?? null;
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

    /**
     * @return array<int, ModrinthGallery>
     */
    public function gallery(): array
    {
        $galleries = [];

        foreach ($this->data['gallery'] ?? [] as $galleryData) {
            $gallery = ModrinthGallery::fromArray($galleryData);

            if ($gallery !== null) {
                $galleries[] = $gallery;
            }
        }

        usort(
            $galleries,
            fn(ModrinthGallery $a, ModrinthGallery $b) => ($a->ordering() ?? 0) <=> ($b->ordering() ?? 0)
        );

        return $galleries;
    }

    public function environment(): array
    {
        return $this->data['environment'] ?? [];
    }

    public function environmentLabel(): string
    {
        $environments = $this->environment();

        if (empty($environments)) {
            return 'Unknown';
        }

        $labels = collect($environments)
            ->map(fn(string $environment) => match ($environment) {
                'client_and_server' => 'Client & Server',
                'client_only' => 'Client only',
                'client_only_server_optional' => 'Client • Server optional',
                'singleplayer_only' => 'Singleplayer only',

                'server_only' => 'Server only',
                'server_only_client_optional' => 'Server • Client optional',
                'dedicated_server_only' => 'Dedicated server only',

                'client_or_server' => 'Client or Server',
                'client_or_server_prefers_both' => 'Client & Server recommended',

                default => 'Unknown',
            })
            ->unique()
            ->values();

        return $labels->join(', ');
    }

    public function links(): array
    {
        return collect($this->data['link_urls'] ?? [])
            ->mapWithKeys(function (array $link, string $key) {
                $url = $link['url'] ?? null;

                if (!$url) {
                    return [];
                }

                return [
                    $key => [
                        'key' => $key,
                        'platform' => $link['platform'] ?? $key,
                        'donation' => (bool) ($link['donation'] ?? false),
                        'url' => $url,
                    ],
                ];
            })
            ->all();
    }

    public function link(string $key): ?array
    {
        return $this->links()[$key] ?? null;
    }

    public function linkUrl(string $key): ?string
    {
        return $this->link($key)['url'] ?? null;
    }

    public function sourceUrl(): ?string
    {
        return $this->linkUrl('source');
    }

    public function issuesUrl(): ?string
    {
        return $this->linkUrl('issues');
    }

    public function wikiUrl(): ?string
    {
        return $this->linkUrl('wiki');
    }

    public function discordUrl(): ?string
    {
        return $this->linkUrl('discord');
    }

    public function raw(): array
    {
        return $this->data;
    }
}
