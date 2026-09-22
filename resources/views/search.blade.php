@php
    /** @var Collection<int, Project> $projects */

    use App\Models\Project;use Illuminate\Support\Collection;$loaderOptions = $projects
        ->flatMap(fn($project) => $project->loaders())
        ->filter()
        ->unique()
        ->sort()
        ->mapWithKeys(
            fn($loader) => [
                $loader => match ($loader) {
                    'neoforge' => 'NeoForge',
                    'spigot' => 'Spigot',
                    'bukkit' => 'Bukkit',
                    'paper' => 'Paper',
                    'purpur' => 'Purpur',
                    'velocity' => 'Velocity',
                    'bungeecord' => 'Bungeecord',
                    default => ucfirst($loader),
                },
            ],
        )
        ->all();

    $versionOptions = $projects
        ->flatMap(fn($project) => $project->versions())
        ->filter()
        ->filter(fn($version) => preg_match('/^\d+\.\d+(?:\.\d+)?$/', $version))
        ->unique()
        ->sort(fn($a, $b) => version_compare($b, $a))
        ->mapWithKeys(
            fn($version) => [
                $version => $version,
            ],
        )
        ->all();
@endphp

<x-layouts.app
    :title="'Search ' . $query . ' - CraftHub'"
    :show-search="true"
    :query="$query"
>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:py-14">
        <header class="pb-7">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div class="min-w-0">

                    <h1
                        class="truncate text-3xl font-bold tracking-tight sm:text-4xl"
                    >
                        @if (filled($query))
                            Results for
                            <span class="text-crafthub font-pixel">
                                {{ $query }}
                            </span>
                        @else
                            Search projects
                        @endif
                    </h1>

                    <p class="mt-2 text-sm text-muted">
                        {{ number_format($total, 0, ',', ' ') }}
                        {{ $total === 1 ? 'project' : 'projects' }}
                        found
                    </p>
                </div>
            </div>
        </header>

        <div class="grid gap-8 lg:grid-cols-[220px_minmax(0,1fr)]">
            <aside>
                <div class="lg:sticky lg:top-24">
                    <div class="mb-2 flex items-center justify-between">
                        <h2 class="text-xs font-semibold uppercase tracking-[0.15em] text-text">
                            Filters
                        </h2>

                        @if (
                            request('version') ||
                            request('loader') ||
                            request('type') ||
                            request('sort')
                        )
                            <a
                                href="{{ route('search', ['q' => request('q')]) }}"
                                class="text-xs text-muted transition-colors hover:text-crafthub"
                            >
                                Reset
                            </a>
                        @endif
                    </div>

                    <form
                        method="GET"
                        action="{{ route('search') }}"
                        class="space-y-5"
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
                            :options="$versionOptions"
                        />

                        <x-select
                            name="loader"
                            label="Loader"
                            placeholder="All loaders"
                            :options="$loaderOptions"
                        />

                        <x-select
                            name="sort"
                            label="Sort by"
                            value="relevance"
                            :options="[
                                'relevance' => 'Relevance',
                                'downloads' => 'Downloads',
                                'newest' => 'Date published',
                                'updated' => 'Date updated',
                                'follows' => 'Followers',
                            ]"
                        />

                        <x-button
                            type="submit"
                            class="w-full"
                        >
                            <i
                                class="fa-pixel fa-regular fa-sliders h-4 w-4"
                            ></i>

                            Apply filters
                        </x-button>
                    </form>

                </div>
            </aside>

            <section class="min-w-0">
                @forelse ($projects as $project)
                    <div class="mb-3">
                        <x-card :project="$project" />
                    </div>
                @empty
                    <div class="border border-white/10 bg-surface px-6 py-20 text-center">
                        <h2 class="mt-5 text-lg font-semibold">
                            No projects found
                        </h2>

                        <p
                            class="mx-auto mt-2 max-w-sm text-sm
                            leading-6 text-muted"
                        >
                            Try another search or remove some filters
                            to find more projects.
                        </p>

                        <a
                            href="{{ route('search') }}"
                            class="mt-6 inline-flex items-center gap-2
                            border border-white/10 bg-surface-light
                            px-4 py-2.5 text-sm font-medium text-text
                            transition-colors
                            hover:border-crafthub/40
                            hover:text-crafthub"
                        >
                            Clear filters
                        </a>
                    </div>

                @endforelse


                @if ($lastPage > 1)

                    @php
                        $start = max(1, $page - 2);
                        $end = min($lastPage, $page + 2);
                    @endphp

                    <nav
                        class="mt-8 flex flex-col gap-5
                        border-t border-white/10 pt-6
                        sm:grid sm:grid-cols-[1fr_auto_1fr]
                        sm:items-center"
                        aria-label="Pagination"
                    >
                        <div class="flex justify-start">
                            @if ($page > 1)

                                <a
                                    href="{{ request()->fullUrlWithQuery([
                                        'page' => $page - 1,
                                    ]) }}"
                                    class="inline-flex items-center gap-2
                                    border border-white/10 bg-surface
                                    px-3.5 py-2 text-sm text-muted
                                    transition-colors
                                    hover:border-crafthub/30
                                    hover:text-text
                                    font-pixel"
                                >
                                    <i class="fa-pixel fa-regular fa-arrow-left h-3.5 w-3.5"></i>
                                    Previous
                                </a>

                            @else

                                <span
                                    class="inline-flex cursor-not-allowed
                                    items-center gap-2 border
                                    border-white/5 bg-surface/40
                                    px-3.5 py-2 text-sm text-muted/30 font-pixel"
                                >
                                    <i class="fa-pixel fa-regular fa-arrow-left h-3.5 w-3.5"></i>
                                    Previous
                                </span>

                            @endif

                        </div>

                        <div
                            class="flex items-center justify-center gap-1"
                        >

                            @if ($start > 1)

                                <a
                                    href="{{ request()->fullUrlWithQuery(['page' => 1]) }}"
                                    class="flex h-8 min-w-8 items-center
                                    justify-center px-2 text-xs
                                    text-muted transition-colors
                                    hover:bg-surface hover:text-text font-pixel"
                                >
                                    1
                                </a>

                                @if ($start > 2)
                                    <span
                                        class="px-1 text-xs text-muted/40"
                                    >
                                        …
                                    </span>
                                @endif

                            @endif


                            @for ($i = $start; $i <= $end; $i++)

                                @if ($i === $page)

                                    <span
                                        class="flex h-8 min-w-8
                                        items-center justify-center
                                        bg-crafthub px-2 text-xs
                                        font-semibold text-background font-pixel"
                                    >
                                        {{ $i }}
                                    </span>

                                @else

                                    <a
                                        href="{{ request()->fullUrlWithQuery([
                                            'page' => $i,
                                        ]) }}"
                                        class="flex h-8 min-w-8
                                        items-center justify-center
                                        px-2 text-xs text-muted
                                        transition-colors
                                        hover:bg-surface
                                        hover:text-text font-pixel"
                                    >
                                        {{ $i }}
                                    </a>

                                @endif

                            @endfor


                            @if ($end < $lastPage)

                                @if ($end < $lastPage - 1)
                                    <span
                                        class="px-1 text-xs text-muted/40"
                                    >
                                        ...
                                    </span>
                                @endif

                                <a
                                    href="{{ request()->fullUrlWithQuery([
                                        'page' => $lastPage,
                                    ]) }}"
                                    class="flex h-8 min-w-8
                                    items-center justify-center
                                    px-2 text-xs text-muted
                                    transition-colors
                                    hover:bg-surface hover:text-text font-pixel"
                                >
                                    {{ $lastPage }}
                                </a>

                            @endif

                        </div>

                        <div class="flex justify-end">
                            @if ($page < $lastPage)
                                <a
                                    href="{{ request()->fullUrlWithQuery(['page' => $page + 1]) }}"
                                    class="inline-flex items-center gap-2
                                    border border-white/10 bg-surface
                                    px-3.5 py-2 text-sm text-muted
                                    transition-colors
                                    hover:border-crafthub/30
                                    hover:text-text
                                    font-pixel"
                                >
                                    Next
                                    <i class="fa-pixel fa-regular fa-arrow-right h-3.5 w-3.5"></i>
                                </a>

                            @else

                                <span
                                    class="inline-flex cursor-not-allowed
                                    items-center gap-2 border
                                    border-white/5 bg-surface/40
                                    px-3.5 py-2 text-sm text-muted/30 font-pixel"
                                >
                                    Next
                                    <i class="fa-pixel fa-regular fa-arrow-right h-3.5 w-3.5"></i>
                                </span>

                            @endif

                        </div>

                    </nav>

                @endif

            </section>
        </div>
    </div>
</x-layouts.app>
