<?php

namespace App\Services;

use App\Contracts\ProjectProvider;
use http\Exception\InvalidArgumentException;

final class ProjectProviderRegistry
{
    /**
     * @var array<string, ProjectProvider>
     */
    private array $providers = [];

    /**
     * @param iterable<ProjectProvider> $providers
     */
    public function __construct(
        iterable $providers
    )
    {
        foreach ($providers as $provider) {
            $this->providers[$provider->key()] = $provider;
        }
    }

    /**
     * @return array<string, ProjectProvider>
     */
    public function all(): array
    {
        return $this->providers;
    }

    public function get(string $key): ProjectProvider
    {
        return $this->providers[$key]
            ?? throw new InvalidArgumentException(
                "Unknown project provider: {$key}"
            );
    }

    public function has(string $key): bool
    {
        return isset($this->providers[$key]);
    }
}
