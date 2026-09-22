@php
    /** @var ProjectDetails $details */
    /** @var Project $project */

    use App\DTO\ProjectDetails;use App\Models\Project;use Carbon\Carbon;

    $projectTypes = collect($project->projectTypes())
        ->filter()
        ->unique()
        ->values();

    $categories = collect($project->categories())
        ->filter()
        ->unique()
        ->values();

    $gameVersions = collect($project->versions())
        ->filter()
        ->unique()
        ->sort(
            fn($a, $b) => version_compare(
                (string) $b,
                (string) $a,
            ),
        )
        ->values();

    $description = $details->description ?: $project->summary();

    $gallery = collect($details->gallery)
        ->filter()
        ->values();

    $versions = collect($details->versions)
        ->filter(fn($version) => is_array($version))
        ->values();

    $projectLinks = collect($details->links)
        ->mapWithKeys(function ($link, $key) {
            if (!is_array($link)) {
                return [];
            }

            $url = $link['url'] ?? null;

            if (!$url) {
                return [];
            }

            $linkKey = is_string($key)
                ? $key
                : ($link['platform'] ?? 'link-' . $key);

            return [
                $linkKey => [
                    ...$link,
                    'url' => $url,
                    'label' => $link['label'] ?? match ($linkKey) {
                        'source' => 'Source code',
                        'issues' => 'Issues',
                        'wiki' => 'Wiki',
                        'discord' => 'Discord',
                        'modrinth' => 'Modrinth',
                        'spigot' => 'SpigotMC',
                        'hangar' => 'Hangar',
                        default => ucfirst($linkKey),
                    },
                ],
            ];
        });

    foreach ($project->sources() as $source) {
        $platform = $source['platform'] ?? null;
        $url = $source['url'] ?? null;

        if (!$platform || !$url) {
            continue;
        }

        $projectLinks->put($platform, [
            'platform' => $platform,
            'url' => $url,
            'label' => match ($platform) {
                'modrinth' => 'Modrinth',
                'spigot' => 'SpigotMC',
                default => ucfirst($platform),
            },
        ]);
    }

    $projectLinks = $projectLinks
        ->filter(fn($link) => !empty($link['url']))
        ->unique('url');

    $downloadLinks = collect();

    if (
        $project->hasSource('modrinth')
        && $details->downloadUrl
    ) {
        $downloadLinks->push([
            'platform' => 'modrinth',
            'label' => 'Download from Modrinth',
            'url' => $details->downloadUrl,
            'external' => false,
        ]);
    }

    foreach ($project->sources() as $source) {
        $platform = $source['platform'] ?? null;

        if (
            !$platform
            || $platform === 'modrinth'
            || empty($source['download_url'])
        ) {
            continue;
        }

        $downloadLinks->push([
            'platform' => $platform,
            'label' => match ($platform) {
                'spigot' => 'Download from SpigotMC',
                default => 'Download from ' . ucfirst($platform),
            },
            'url' => $source['download_url'],
            'external' => true,
        ]);
    }

    if (
        $downloadLinks->isEmpty()
        && $details->downloadUrl
    ) {
        $source = collect($project->sources())->first();

        $platform = $source['platform'] ?? 'project';

        $downloadLinks->push([
            'platform' => $platform,
            'label' => match ($platform) {
                'modrinth' => 'Download from Modrinth',
                'spigot' => 'Download from SpigotMC',
                default => 'Download',
            },
            'url' => $details->downloadUrl,
            'external' => $platform !== 'modrinth',
        ]);
    }

    $downloads = collect($details->downloads)
        ->filter(
            fn($download) =>
                is_array($download)
                && !empty($download['url']),
        )
        ->values();

    $downloadLoaders = $downloads
        ->flatMap(fn(array $download) => $download['loaders'] ?? [])
        ->filter()
        ->map(
            fn($loader) => strtolower((string) $loader),
        )
        ->unique()
        ->sort()
        ->mapWithKeys(
            fn($loader) => [
                $loader => ucfirst($loader),
            ],
        );

    $downloadGameVersions = $downloads
        ->flatMap(
            fn(array $download) => $download['game_versions'] ?? [],
        )
        ->filter()
        ->map(
            fn($version) => (string) $version,
        )
        ->unique()
        ->sort(
            fn($a, $b) => version_compare($b, $a),
        )
        ->mapWithKeys(
            fn($version) => [
                $version => 'Minecraft ' . $version,
            ],
        );
@endphp

<x-layouts.app
    :title="$project->name() . ' - CraftHub'"
    :show-search="true"
>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:py-12">
        <header>
            <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
                <div class="shrink-0">
                    @if ($project->iconUrl())
                        <img
                            src="{{ $project->iconUrl() }}"
                            alt="{{ $project->name() }}"
                            class="h-24 w-24 object-cover sm:h-28 sm:w-28"
                        />
                    @else
                        <div class="flex h-24 w-24 items-center justify-center sm:h-28 sm:w-28 bg-surface">
                            <img
                                src="{{ asset('crafthub.svg') }}"
                                alt=""
                                class="h-11 w-11 opacity-20"
                            />
                        </div>
                    @endif
                </div>

                <div class="min-w-0 flex-1">
                    <h1 class="text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl">
                        {{ $project->name() }}
                    </h1>

                    @if ($project->author())
                        <p class="mt-2 text-sm text-muted">
                            by
                            <span class="text-text">
                                {{ $project->author() }}
                            </span>
                        </p>
                    @endif

                    @if ($project->summary())
                        <p class="mt-4 max-w-3xl text-sm leading-6 text-muted sm:text-base sm:leading-7">
                            {{ $project->summary() }}
                        </p>
                    @endif

                    <div class="mt-5 flex flex-wrap gap-x-5 gap-y-2">

                        <span class="inline-flex items-center gap-2 text-xs text-muted">
                            <i class="fa-pixel fa-regular fa-arrow-down-to-bracket h-3.5 w-3.5"></i>
                            {{ number_format($project->downloads(), 0, ',', ' ') }}
                            downloads
                        </span>

                        <span class="inline-flex items-center gap-2 text-xs text-muted">
                            <i class="fa-pixel fa-regular fa-heart h-3.5 w-3.5"></i>
                            {{ number_format($details->likes, 0, ',', ' ') }}
                            followers
                        </span>
                    </div>
                </div>

                @if ($downloads->isNotEmpty())
                    <x-button
                        x-data
                        type="button"
                        @click="$dispatch('open-download-modal')"
                    >
                        <i class="fa-pixel fa-regular fa-arrow-down-to-bracket"></i>
                        Download
                    </x-button>
                @endif
            </div>
        </header>

        <div class="grid gap-8 pt-8 lg:grid-cols-[minmax(0,1fr)_280px]">
            <article
                x-data="{ tab: 'description' }"
                class="min-w-0"
            >
                <div class="mb-7 flex items-stretch gap-2">
                    <button
                        type="button"
                        @click="tab = 'description'"
                        class="px-4 py-3 text-sm font-medium font-pixel transition-colors cursor-pointer"
                        :class="
                            tab === 'description'
                                ? 'bg-crafthub text-background'
                                : 'text-muted hover:text-text'
                        "
                    >
                        Description
                    </button>

                    <button
                        type="button"
                        @click="tab = 'gallery'"
                        class="px-4 py-3 text-sm font-medium font-pixel transition-colors cursor-pointer"
                        :class="
                            tab === 'gallery'
                                ? 'bg-crafthub text-background'
                                : 'text-muted hover:text-text'
                        "
                    >
                        Gallery
                    </button>

                    <button
                        type="button"
                        @click="tab = 'versions'"
                        class="px-4 py-3 text-sm font-medium font-pixel transition-colors cursor-pointer"
                        :class="
                            tab === 'versions'
                                ? 'bg-crafthub text-background'
                                : 'text-muted hover:text-text'
                        "
                    >
                        Versions
                    </button>
                </div>

                <div x-show="tab === 'description'" x-cloak>
                    @if ($description)
                        <div class="markdown">
                            {!! Str::markdown($description) !!}
                        </div>
                    @else
                        <div class="border border-white/10 bg-surface px-6 py-14 text-center">
                            <i class="fa-pixel fa-regular fa-file mx-auto h-5 w-5 text-muted/50"></i>
                            <p class="mt-3 text-sm text-muted">
                                No description available for this project.
                            </p>
                        </div>

                    @endif
                </div>

                <div x-show="tab === 'gallery'" x-cloak>
                    @if ($gallery->isNotEmpty())
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach ($gallery as $image)
                                @php
                                    $imageUrl = is_array($image)
                                        ? ($image['url'] ?? null)
                                        : $image->url();

                                    $imageTitle = is_array($image)
                                        ? ($image['title'] ?? null)
                                        : $image->title();

                                    $imageDescription = is_array($image)
                                        ? ($image['description'] ?? null)
                                        : $image->description();
                                @endphp
                                @if ($imageUrl)
                                    <a
                                        href="{{ $imageUrl }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="group block overflow-hiddenbg-surface bg-surface"
                                    >
                                        <div class="aspect-video overflow-hidden">
                                            <img
                                                src="{{ $imageUrl }}"
                                                alt="{{ $imageTitle ?: $project->name() }}"
                                                loading="lazy"
                                                class="h-full w-full object-cover"
                                            />
                                        </div>

                                        @if ($imageTitle || $imageDescription)
                                            <div class="border-t border-white/5 p-4">
                                                @if ($imageTitle)
                                                    <h3 class="text-sm font-medium text-text">
                                                        {{ $imageTitle }}
                                                    </h3>
                                                @endif

                                                @if ($imageDescription)
                                                    <p class="mt-1 text-xs leading-5 text-muted">
                                                        {{ $imageDescription }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endif
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="border border-white/10 bg-surface px-6 py-14 text-center">
                            <i class="fa-pixel fa-regular fa-images mx-auto h-5 w-5 text-muted/50"></i>
                            <p class="mt-3 text-sm text-muted">
                                No gallery available for this project.
                            </p>
                        </div>
                    @endif
                </div>

                <div x-show="tab === 'versions'" x-cloak>
                    @if ($versions->isNotEmpty())
                        <div class="overflow-hidden border border-white/10 bg-surface">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="border-b border-white/10 bg-background/50">
                                    <tr>
                                        <th class="px-4 py-3 text-[10px] uppercase tracking-wider text-muted">
                                            Version
                                        </th>

                                        <th class="px-4 py-3 text-[10px] uppercase tracking-wider text-muted">
                                            Minecraft
                                        </th>

                                        <th class="px-4 py-3 text-[10px] uppercase tracking-wider text-muted">
                                            Loader
                                        </th>

                                        <th class="px-4 py-3 text-right text-[10px] uppercase tracking-wider text-muted">
                                            Download
                                        </th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach ($versions as $version)
                                        @php
                                            $primaryFile = collect($version['files'] ?? [])
                                                ->firstWhere('primary', true)
                                                ?? collect($version['files'] ?? [])->first();

                                            $gameVersions = collect($version['game_versions'] ?? [])
                                                ->filter()
                                                ->values();

                                            $loaders = collect($version['loaders'] ?? [])
                                                ->filter()
                                                ->values();
                                        @endphp

                                        <tr class="border-b border-white/5 last:border-0 hover:bg-white/2">
                                            <td class="px-4 py-4">
                                                <div class="min-w-0">
                                                    <p class="text-xs font-medium text-text">
                                                        {{
                                                            $version['name']
                                                            ?? $version['version_number']
                                                            ?? 'Unknown version'
                                                        }}
                                                    </p>

                                                    @if (!empty($version['version_number']) && !empty($version['name']) && $version['name'] !== $version['version_number'])
                                                        <p class="mt-1 text-[10px] text-muted">
                                                            {{ $version['version_number'] }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="px-4 py-4">
                                                @if ($gameVersions->isNotEmpty())
                                                    <div class="flex flex-wrap gap-1">
                                                        <span class="text-[12px] text-muted">
                                                            {{ implode(', ', $version['game_versions']) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="px-4 py-4">
                                                @if ($loaders->isNotEmpty())
                                                    <div class="flex flex-wrap gap-1">
                                                        <span class="text-[12px] capitalize text-muted">
                                                            {{ implode(', ', $version['loaders']) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="px-4 py-4 text-right">
                                                @if ($primaryFile && !empty($primaryFile['url']))
                                                    <x-button
                                                        href="{{ $primaryFile['url'] }}"
                                                        class="text-[10px]"
                                                    >
                                                        <i class="fa-pixel fa-regular fa-arrow-down-to-bracket"></i>
                                                        Download
                                                    </x-button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="border border-white/10 bg-surface px-6 py-14 text-center">
                            <i class="fa-pixel fa-regular fa-layer-group mx-auto h-5 w-5 text-muted/50"></i>
                            <p class="mt-3 text-sm text-muted">
                                No versions available.
                            </p>
                        </div>
                    @endif
                </div>
            </article>

            <aside class="space-y-4">
                <section class="border border-white/10 bg-surface p-5">
                    <div class="flex items-center gap-2">
                        <i class="fa-pixel fa-regular fa-info-circle h-4 w-4 text-crafthub"></i>
                        <h2 class="text-sm font-pixel">
                            Information
                        </h2>
                    </div>

                    <dl class="mt-4 space-y-4">
                        @if ($details->environment)
                            <div class="space-y-1">
                                <dt class="text-[10px] uppercase tracking-wider text-muted">
                                    Environment
                                </dt>
                                <dd class="text-sm leading-relaxed text-text">
                                    {{ $details->environment }}
                                </dd>
                            </div>
                        @endif

                        @if ($details->publishedAt)
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-[10px] uppercase tracking-wider text-muted">
                                    Published
                                </dt>
                                <dd class="text-sm text-text">
                                    {{ Carbon::parse($details->publishedAt)->format('M j, Y') }}
                                </dd>
                            </div>
                        @endif

                        @if ($details->updatedAt)
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-[10px] uppercase tracking-wider text-muted">
                                    Updated
                                </dt>
                                <dd class="text-sm text-text">
                                    {{ Carbon::parse($details->updatedAt)->format('M j, Y') }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </section>

                @if ($projectLinks->isNotEmpty())
                    <section class="border border-white/10 bg-surface p-5">
                        <div class="flex items-center gap-2">
                            <i class="fa-pixel fa-regular fa-link h-4 w-4 text-crafthub"></i>
                            <h2 class="text-sm font-pixel">
                                Links
                            </h2>
                        </div>

                        <div class="mt-3">
                            @foreach ($projectLinks as $key => $link)
                                <a
                                    href="{{ $link['url'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="group flex items-center gap-3 pb-2"
                                >
                                    <div class="min-w-0 flex justify-between items-center w-full">
                                        <p class="truncate text-sm font-medium text-text group-hover:text-crafthub">
                                            {{ $link['label'] }}
                                        </p>

                                        <p class="truncate text-[10px] text-muted">
                                            {{ parse_url($link['url'], PHP_URL_HOST) }}
                                        </p>
                                    </div>
                                    <i class="fa-pixel fa-regular fa-arrow-up-right-from-square h-3.5 w-3.5 text-muted/40 group-hover:text-crafthub"></i>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($categories->isNotEmpty())
                    <section class="border border-white/10 bg-surface p-5">
                        <div class="flex items-center gap-2">
                            <i class="fa-pixel fa-regular fa-tag h-4 w-4 text-crafthub"></i>
                            <h2 class="text-sm font-pixel">
                                Categories
                            </h2>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-1.5">
                            @foreach ($categories as $category)
                                <span
                                    class="border border-white/10 bg-background px-2 py-1 text-[10px] capitalize text-muted"
                                >
                                    {{ $category }}
                                </span>
                            @endforeach
                        </div>
                    </section>
                @endif
            </aside>
        </div>
    </div>

    @if ($downloads->isNotEmpty())
        <x-modal-download
            :downloads="$downloads"
            :project="$project"
            :download-loaders="$downloadLoaders"
            :download-game-versions="$downloadGameVersions"
        />
    @endif

</x-layouts.app>
