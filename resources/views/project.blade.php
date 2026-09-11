@php
    /** @var \App\DTO\ProjectDetails $details */
    /** @var \App\Models\Project $project */

    use Carbon\Carbon;$projectTypes = collect($project->projectTypes())
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

    $description = $details->description
        ?: $project->summary();

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
@endphp

<x-layouts.app :title="$project->name() . ' - CraftHub'" :show-search="true">
    <div class="mx-auto max-w-7xl px-6 py-10 lg:py-14">
        <section>
            <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
                <div class="shrink-0">
                    @if (!empty($project->iconUrl()))
                        <img
                            src="{{ $project->iconUrl() }}"
                            alt="{{ $project->name() }}"
                            class="h-24 w-24 rounded-2xl object-cover sm:h-28 sm:w-28"
                        />
                    @else
                        <div
                            class="flex h-24 w-24 items-center justify-center rounded-2xl border border-white/10 bg-surface sm:h-28 sm:w-28"
                        >
                            <img
                                src="{{ asset('crafthub.svg') }}"
                                alt=""
                                class="h-12 w-12 opacity-40"
                            />
                        </div>
                    @endif
                </div>

                <div class="min-w-0 flex-1">
                    <h1
                        class="text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl"
                    >
                        {{ $project->name() }}
                    </h1>

                    @if (!empty($project->summary()))
                        <p
                            class="mt-4 max-w-3xl text-base leading-7 text-muted sm:text-lg"
                        >
                            {{ $project->summary() }}
                        </p>
                    @endif

                    <div
                        class="mt-5 flex flex-wrap items-center gap-x-6 gap-y-3"
                    >
                        <div class="flex items-center gap-2 text-sm text-muted">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                class="h-4 w-4"
                            >
                                <path d="M12 3v12"></path>
                                <path d="m7 10 5 5 5-5"></path>
                                <path d="M5 21h14"></path>
                            </svg>

                            <span>
                                {{
                                    number_format(
                                        $project->downloads(),
                                        0,
                                        ',',
                                        ' ',
                                    )
                                }} downloads
                            </span>
                        </div>

                        @if ($details->followers > 0)
                            <div class="flex items-center gap-2 text-sm text-muted">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    class="h-4 w-4"
                                >
                                    <path
                                        d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z"
                                    />
                                </svg>

                                <span>
                                    {{ number_format($details->followers, 0, ',', ' ') }}
                                    followers
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
                @if ($downloadLinks->isNotEmpty())
                    <div class="mt-6 flex shrink-0 flex-wrap gap-3 sm:mt-0">
                        @foreach ($downloadLinks as $index => $download)
                            <a
                                href="{{ $download['url'] }}"
                                @if ($download['external'])
                                    target="_blank"
                                rel="noopener noreferrer"
                                @endif
                                @class([
                                    'inline-flex items-center gap-2 rounded-xl px-5 py-3 text-sm font-semibold transition',
                                    'bg-crafthub text-background hover:bg-crafthub-light' =>
                                        $index === 0,
                                    'border border-white/10 bg-surface text-text hover:border-crafthub/30' =>
                                        $index !== 0,
                                ])
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    class="h-4 w-4"
                                >
                                    <path d="M12 3v12" />
                                    <path d="m7 10 5 5 5-5" />
                                    <path d="M5 21h14" />
                                </svg>

                                {{ $download['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <div class="my-10 border-t border-white/5"></div>

        <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_320px]">
            <article x-data="{ tab: 'description' }" class="min-w-0">
                <div
                    class="mb-6 flex items-center gap-6 border-b border-white/5"
                >
                    <button
                        type="button"
                        @click="tab = 'description'"
                        class="pb-3 text-sm font-semibold transition"
                        :class="tab === 'description'
                            ? 'border-b-2 border-crafthub text-text'
                            : 'text-muted hover:text-text'"
                    >
                        Description
                    </button>

                    <button
                        type="button"
                        @click="tab = 'gallery'"
                        class="pb-3 text-sm font-semibold transition"
                        :class="tab === 'gallery'
                            ? 'border-b-2 border-crafthub text-text'
                            : 'text-muted hover:text-text'"
                    >
                        Gallery
                    </button>
                </div>

                <div x-show="tab === 'description'" x-cloak>
                    @if (!empty($description))
                        <div
                            class="prose prose-invert max-w-none prose-headings:text-text prose-p:text-muted prose-strong:text-text prose-a:text-crafthub prose-a:no-underline hover:prose-a:text-crafthub-light prose-code:text-crafthub-light prose-pre:border prose-pre:border-white/10 prose-pre:bg-surface"
                        >
                            {!!
                                Str::markdown(
                                    $description,
                                )
                            !!}
                        </div>
                    @else
                        <div
                            class="rounded-2xl border border-white/10 bg-surface px-6 py-14 text-center"
                        >
                            <p class="text-sm text-muted">No description available for this project.</p>
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
                                        class="group overflow-hidden rounded-2xl border border-white/10 bg-surface transition hover:border-crafthub/30"
                                    >
                                        <div class="aspect-video overflow-hidden bg-background">
                                            <img
                                                src="{{ $imageUrl }}"
                                                alt="{{ $imageTitle ?: $project->name() }}"
                                                loading="lazy"
                                                class="h-full w-full object-cover"
                                            />
                                        </div>

                                        @if ($imageTitle || $imageDescription)
                                            <div class="p-4">
                                                @if ($imageTitle)
                                                    <h3 class="text-sm font-semibold text-text">
                                                        {{ $imageTitle }}
                                                    </h3>
                                                @endif

                                                @if ($imageDescription)
                                                    <p class="mt-1 text-sm leading-6 text-muted">
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
                        <div
                            class="rounded-2xl border border-white/10 bg-surface px-6 py-14 text-center"
                        >
                            <p class="text-sm text-muted">
                                No gallery available for this project.
                            </p>
                        </div>
                    @endif
                </div>
            </article>

            <aside class="space-y-5">
                <div class="rounded-2xl border border-white/10 bg-surface p-5">
                    <h2 class="font-semibold">Project information</h2>

                    <div class="mt-5 space-y-4">
                        @if (!$projectTypes->isNotEmpty())
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider text-muted">
                                    Type
                                </p>

                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach ($projectTypes as $type)
                                        <span
                                            class="rounded-lg border border-crafthub/15 bg-crafthub/10 px-2.5 py-1 text-xs font-medium capitalize text-crafthub"
                                        >
                                            {{ $type }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($details->environment)
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider text-muted">Environment</p>

                                <p class="mt-1 text-sm text-text">
                                    {{ $details->environment }}
                                </p>
                            </div>
                        @endif

                        {{--                        @if ($details->lisence)--}}
                        {{--                            <div>--}}
                        {{--                                <p--}}
                        {{--                                    class="text-xs font-medium uppercase tracking-wider text-muted"--}}
                        {{--                                >License</p>--}}

                        {{--                                <p class="mt-1 text-sm text-text">--}}
                        {{--                                    {{--}}
                        {{--                                        $project--}}
                        {{--                                            ->license()--}}
                        {{--                                            ->name()--}}
                        {{--                                    }}--}}
                        {{--                                </p>--}}
                        {{--                            </div>--}}
                        {{--                        @endif--}}

                        @if ($details->publishedAt)
                            <div>
                                <p
                                    class="text-xs font-medium uppercase tracking-wider text-muted"
                                >Published</p>

                                <p class="mt-1 text-sm text-text">
                                    {{
                                        Carbon::parse(
                                            $details->publishedAt,
                                        )->format('M j, Y')
                                    }}
                                </p>
                            </div>
                        @endif

                        @if ($details->updatedAt)
                            <div>
                                <p
                                    class="text-xs font-medium uppercase tracking-wider text-muted"
                                >Updated</p>

                                <p class="mt-1 text-sm text-text">
                                    {{
                                        Carbon::parse(
                                            $details->updatedAt,
                                        )->format('M j, Y')
                                    }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                @if ($projectLinks->isNotEmpty())
                    <div
                        class="rounded-2xl border border-white/10 bg-surface p-5"
                    >
                        <h2 class="font-semibold">Links</h2>

                        <div class="mt-4 space-y-2">
                            @foreach ($projectLinks as $key => $link)
                                <a
                                    href="{{ $link['url'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="group flex w-full items-center gap-3 rounded-xl border border-white/5 bg-background px-3.5 py-3 text-sm text-muted transition hover:border-crafthub/30 hover:text-text"
                                >
                                    <div
                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white/5 text-muted transition group-hover:bg-crafthub/10 group-hover:text-crafthub"
                                    >
                                        @switch ($key)
                                            @case ('source')
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="h-4 w-4"
                                                >
                                                    <path d="m18 16 4-4-4-4" />
                                                    <path d="m6 8-4 4 4 4" />
                                                    <path d="m14.5 4-5 16" />
                                                </svg>
                                                @break
                                            @case ('issues')
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="h-4 w-4"
                                                >
                                                    <circle cx="12" cy="12" r="10" />
                                                    <path d="M12 8v4" />
                                                    <path d="M12 16h.01" />
                                                </svg>
                                                @break
                                            @case ('wiki')
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="h-4 w-4"
                                                >
                                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                                    <path
                                                        d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z" />
                                                </svg>
                                                @break
                                            @case ('discord')
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="h-4 w-4"
                                                >
                                                    <path d="M8 12h.01" />
                                                    <path d="M16 12h.01" />
                                                    <path d="M7.5 7.2A10.5 10.5 0 0 1 12 6a10.5 10.5 0 0 1 4.5 1.2" />
                                                    <path d="M8.5 17.5a6.6 6.6 0 0 0 7 0" />
                                                </svg>
                                                @break
                                            @default
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="h-4 w-4"
                                                >
                                                    <path
                                                        d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                                                    <path
                                                        d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                                                </svg>
                                        @endswitch
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-text">
                                            {{ $link['label'] }}
                                        </p>

                                        <p class="truncate text-xs text-muted">
                                            {{
                                                parse_url(
                                                    $link['url'],
                                                    PHP_URL_HOST,
                                                )
                                            }}
                                        </p>
                                    </div>

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="h-4 w-4 shrink-0 opacity-30 transition group-hover:opacity-100"
                                    >
                                        <path d="M15 3h6v6" />
                                        <path d="M10 14 21 3" />
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($categories->isNotEmpty())
                    <div
                        class="rounded-2xl border border-white/10 bg-surface p-5"
                    >
                        <h2 class="font-semibold">Categories</h2>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($categories as $category)
                                <span
                                    class="rounded-lg border border-white/5 bg-background px-2.5 py-1.5 text-xs capitalize text-muted"
                                >
                                    {{ $category }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div
                    x-data="{ showAll: false }"
                    class="rounded-2xl border border-white/10 bg-surface p-5"
                >
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="font-semibold">Versions</h2>

                        <span class="text-xs text-muted">
                            {{ $versions->count() ?: $gameVersions->count() }}
                        </span>
                    </div>

                    @if ($versions->isNotEmpty())
                        <div class="mt-4 space-y-2">
                            @foreach ($versions as $index => $version)
                                @php
                                    $primaryFile = collect($version['files'] ?? [])
                                        ->firstWhere('primary', true)
                                        ?? collect($version['files'] ?? [])->first();
                                @endphp

                                <div
                                    x-show="showAll || {{ $index }} < 6"
                                    x-cloak
                                    class="rounded-xl border border-white/5 bg-background p-3.5"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium text-text">
                                                {{
                                                    $version['name']
                                                    ?? $version['version_number']
                                                    ?? 'Unknown version'
                                                }}
                                            </p>

                                            @if (!empty($version['game_versions']))
                                                <p class="mt-1 truncate text-xs text-muted">
                                                    {{
                                                        implode(
                                                            ', ',
                                                            array_slice(
                                                                $version['game_versions'],
                                                                0,
                                                                4,
                                                            ),
                                                        )
                                                    }}
                                                </p>
                                            @endif
                                        </div>

                                        @if ($primaryFile && !empty($primaryFile['url']))
                                            <a
                                                href="{{ $primaryFile['url'] }}"
                                                class="rounded-lg bg-crafthub px-2.5 py-1.5 text-xs font-semibold text-background"
                                            >
                                                Download
                                            </a>
                                        @endif
                                    </div>

                                    @if (!empty($version['loaders']))
                                        <div class="mt-3 flex flex-wrap gap-1.5">
                                            @foreach ($version['loaders'] as $loader)
                                                <span
                                                    class="rounded-md border border-white/5 px-1.5 py-0.5 text-[10px] capitalize text-muted"
                                                >
                                                    {{ $loader }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif ($gameVersions->isNotEmpty())
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($gameVersions as $version)
                                <span
                                    class="rounded-lg border border-white/5 bg-background px-2.5 py-1.5 text-xs text-muted"
                                >
                                    Minecraft {{ $version }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-4 text-sm text-muted">
                            No versions available.
                        </p>
                    @endif

                    @if ($versions->count() > 6)
                        <button
                            type="button"
                            @click="showAll = !showAll"
                            class="mt-4 w-full rounded-xl border border-white/10 px-4 py-2.5 text-sm font-medium text-muted"
                        >
                            <span x-show="!showAll">
                                View all {{ $versions->count() }} versions
                            </span>

                            <span x-show="showAll">Show less</span>
                        </button>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</x-layouts.app>
