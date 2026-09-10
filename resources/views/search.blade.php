@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\ModrinthProject> $projects */

    $versionOption = $projects
        ->flatMap(fn($p) => $p->gameVersions() ?? [])
        ->filter()
        ->filter(fn($v) => preg_match('/^\d+\.\d+(?:\.\d+)?$/', $v))
        ->unique()
        ->sort(fn($a, $b) => version_compare($b, $a))
        ->mapWithKeys(
            fn($version) => [
                $version => $version,
            ],
        )
        ->all();

    $knownLoaders = [
        'forge',
        'neoforge',
        'fabric',
        'quilt',
        'paper',
        'purpur',
        'velocity',
        'bukkit',
        'spigot',
        'folia',
    ];

    $loaderOptions = $projects
        ->flatMap(fn($project) => $project->categories() ?? [])
        ->map(fn($category) => strtolower($category))
        ->filter(fn($category) => in_array($category, $knownLoaders, true))
        ->unique()
        ->sort()
        ->mapWithKeys(
            fn($loader) => [
                $loader => match ($loader) {
                    'neoforge' => 'NeoForge',
                    default => ucfirst($loader),
                },
            ],
        )
        ->all();

    $typeLabels = [
        'mod' => 'Mod',
        'plugin' => 'Plugin',
        'modpack' => 'Modpack',
        'resourcepack' => 'Resource Pack',
        'shader' => 'Shader',
    ];

    $typeOptions = $projects
        ->flatMap(fn($project) => $project->projectTypes() ?? [])
        ->filter()
        ->unique()
        ->sort()
        ->mapWithKeys(
            fn($type) => [
                $type => $typeLabels[$type] ?? ucfirst($type),
            ],
        )
        ->all();
@endphp

<x-layouts.app
    :title="'Search ' . $query . ' — CraftHub'"
    :show-search="true"
    :query="$query"
>
    <div class="mx-auto max-w-7xl px-6 py-10 lg:py-14">
        <div class="mb-10">
            <p class="text-sm font-semibold text-crafthub">Search results</p>
            <div
                class="mt-2 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">
                        {{
                            filled($query)
                                ? 'Result for'
                                : 'No research'
                        }}

                        @if (filled($query))
                            <span class="text-crafthub"> {{ $query }} </span>
                        @endif
                    </h1>

                    <p class="mt-2 text-sm text-muted">
                        {{
                            number_format(
                                $total,
                                0,
                                ',',
                                ' ',
                            )
                        }} {{
                            $total === 1
                                ? 'project'
                                : 'projects'
                        }} found
                    </p>
                </div>
            </div>
        </div>

        <div class="grid gap-10 lg:grid-cols-[240px_minmax(0,1fr)]">
            <aside>
                <div class="lg:sticky lg:top-28">
                    <div class="mb-5 flex items-center justify-between">
                        <h2 class="font-semibold">Filters</h2>

                        @if (
                            request('version') ||
                            request('loader') ||
                            request('type') ||
                            request('sort')
                        )
                            <a
                                href="{{ route('search', ['q' => request('q')]) }}"
                                class="text-xs font-medium text-muted transition hover:text-crafthub"
                            >
                                Reset
                            </a>

                        @endif
                    </div>

                    <form
                        method="GET"
                        action="{{ route('search') }}"
                        class="space-y-6"
                    >
                        <input
                            type="hidden"
                            name="q"
                            value="{{ request('q') }}"
                        />
                        <x-select
                            name="version"
                            label="Minecraft version"
                            placeholder="All versions"
                            :options="$versionOption"
                        />

                        <x-select
                            name="loader"
                            label="Loader"
                            placeholder="All loaders"
                            :options="$loaderOptions"
                        />

                        <x-select
                            name="type"
                            label="Project Type"
                            placeholder="All types"
                            :options="$typeOptions"
                        />

                        <x-select
                            name="sort"
                            label="Sort by"
                            value="relevance"
                            :options="[
                                'relevance' => 'Relevance',
                                'downloads' => 'Downloads',
                                'newest' => 'Data published',
                                'updated' => 'Date updated',
                                'follows' => 'Followers',
                            ]"
                        />

                        <button
                            type="submit"
                            class="w-full rounded-xl bg-crafthub px-4 py-2.5 text-sm font-semibold text-background transition hover:bg-crafthub-light"
                        >
                            Apply filters
                        </button>
                    </form>
                </div>
            </aside>
            <section>
                <div class="space-y-3">
                    @forelse ($projects as $project)
                        <x-card :project="$project" />
                    @empty
                        <div
                            class="rounded-2xl border border-white/10 bg-surface px-6 py-20 text-center"
                        >
                            <div
                                class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-crafthub/10 text-crafthub"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    class="h-6 w-6"
                                >
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.3-4.3"></path>
                                </svg>
                            </div>

                            <h2 class="mt-5 text-lg font-semibold">
                                No projects found
                            </h2>

                            <p
                                class="mx-auto mt-2 max-w-sm text-sm leading-6 text-muted"
                            >Try another search or remove some filters to find more projects.</p>

                            <a
                                href="{{ route('search', [
                                    'q' => request('q')
                                ]) }}"
                                class="mt-6 inline-flex rounded-xl bg-crafthub px-4 py-2.5 text-sm font-semibold text-background transition hover:bg-crafthub-light"
                            >
                                Clear filters
                            </a>
                        </div>
                    @endforelse
                    @if ($lastPage > 1)
                        <nav
                            class="mt-8 flex items-center justify-between gap-4"
                            aria-label="Pagination"
                        >
                            @if ($page > 1)
                                <a
                                    href="{{ request()->fullUrlWithQuery([
                                        'page' => $page - 1,
                                    ]) }}"
                                    class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-surface px-4 py-2.5 text-sm font-medium text-muted transition hover:border-crafthub/30 hover:text-text"
                                >
                                    <span>←</span>
                                    Previous
                                </a>
                            @else
                                <span
                                    class="inline-flex cursor-not-allowed items-center gap-2 rounded-xl border border-white/5 bg-surface/50 px-4 py-2.5 text-sm text-muted/40"
                                >
                                    <span>←</span>
                                    Previous
                                </span>
                            @endif

                            <div class="flex items-center gap-1">
                                @php
                                    $start = max(1, $page - 2);
                                    $end = min($lastPage, $page + 2);
                                @endphp

                                @if ($start > 1)
                                    <a
                                        href="{{ request()->fullUrlWithQuery(['page' => 1]) }}"
                                        class="flex h-9 min-w-9 items-center justify-center rounded-lg px-2 text-sm text-muted transition hover:bg-surface hover:text-text"
                                    >
                                        1
                                    </a>

                                    @if ($start > 2)
                                        <span class="px-1 text-muted"> … </span>
                                    @endif
                                @endif

                                @for ($i = $start; $i <= $end; $i++)
                                    @if ($i === $page)
                                        <span
                                            class="flex h-9 min-w-9 items-center justify-center rounded-lg bg-crafthub px-2 text-sm font-semibold text-background"
                                        >
                                            {{ $i }}
                                        </span>
                                    @else
                                        <a
                                            href="{{ request()->fullUrlWithQuery([
                                                'page' => $i,
                                            ]) }}"
                                            class="flex h-9 min-w-9 items-center justify-center rounded-lg px-2 text-sm text-muted transition hover:bg-surface hover:text-text"
                                        >
                                            {{ $i }}
                                        </a>
                                    @endif
                                @endfor

                                @if ($end < $lastPage)
                                    @if ($end < $lastPage - 1)
                                        <span class="px-1 text-muted"> … </span>
                                    @endif

                                    <a
                                        href="{{ request()->fullUrlWithQuery(['page' => $lastPage]) }}"
                                        class="flex h-9 min-w-9 items-center justify-center rounded-lg px-2 text-sm text-muted transition hover:bg-surface hover:text-text"
                                    >
                                        {{ $lastPage }}
                                    </a>
                                @endif
                            </div>

                            @if ($page < $lastPage)
                                <a
                                    href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}"
                                    class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-surface px-4 py-2.5 text-sm font-medium text-muted transition hover:border-crafthub/30 hover:text-text"
                                >
                                    Next
                                    <span>→</span>
                                </a>
                            @else
                                <span
                                    class="inline-flex cursor-not-allowed items-center gap-2 rounded-xl border border-white/5 bg-surface/50 px-4 py-2.5 text-sm text-muted/40"
                                >
                                    Next
                                    <span>→</span>
                                </span>
                            @endif
                        </nav>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>
