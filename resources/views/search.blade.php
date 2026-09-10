@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\ModrinthProject> $projects */
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
                        <div>
                            <label
                                for="version"
                                class="mb-2 block text-sm font-medium text-text"
                            >
                                Minecraft version
                            </label>

                            <select
                                id="version"
                                name="version"
                                class="w-full rounded-xl border border-white/10 bg-surface px-3 py-2.5 text-sm text-text outline-none transition focus:border-crafthub/50 focus:ring-4 focus:ring-crafthub/5"
                            >
                                <option value="">All versions</option>

                                @foreach ([
                                        '1.21.8',
                                        '1.21.5',
                                        '1.21.4',
                                        '1.21.1',
                                        '1.20.6',
                                        '1.20.4',
                                        '1.20.1',
                                        '1.19.4',
                                        '1.19.2'
                                    ]
                                    as $version)
                                    <option
                                        value="{{ $version }}"
                                        @selected (request('version') === $version)
                                    >
                                        {{ $version }}
                                    </option>

                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label
                                for="loader"
                                class="mb-2 block text-sm font-medium text-text"
                            >
                                Loader
                            </label>

                            <select
                                id="loader"
                                name="loader"
                                class="w-full rounded-xl border border-white/10 bg-surface px-3 py-2.5 text-sm outline-none transition focus:border-crafthub/50 focus:ring-4 focus:ring-crafthub/5"
                            >
                                <option value="">All loaders</option>

                                @foreach ([
                                        'forge',
                                        'neoforge',
                                        'fabric',
                                        'quilt',
                                        'paper',
                                        'purpur',
                                        'velocity',
                                        'bukkit',
                                        'spigot'
                                    ]
                                    as $loader)
                                    <option
                                        value="{{ $loader }}"
                                        @selected (request('loader') === $loader)
                                    >
                                        {{
                                            ucfirst(
                                                $loader,
                                            )
                                        }}
                                    </option>

                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label
                                for="type"
                                class="mb-2 block text-sm font-medium text-text"
                            >
                                Project type
                            </label>

                            <select
                                id="type"
                                name="type"
                                class="w-full rounded-xl border border-white/10 bg-surface px-3 py-2.5 text-sm outline-none transition focus:border-crafthub/50 focus:ring-4 focus:ring-crafthub/5"
                            >
                                <option value="">All types</option>

                                <option
                                    value="mod"
                                    @selected (request('type') === 'mod')
                                >
                                    Mod
                                </option>

                                <option
                                    value="plugin"
                                    @selected (request('type') === 'plugin')
                                >
                                    Plugin
                                </option>

                                <option
                                    value="modpack"
                                    @selected (request('type') === 'modpack')
                                >
                                    Modpack
                                </option>

                                <option
                                    value="resourcepack"
                                    @selected (request('type') === 'resourcepack')
                                >
                                    Resource Pack
                                </option>

                                <option
                                    value="shader"
                                    @selected (request('type') === 'shader')
                                >
                                    Shader
                                </option>
                            </select>
                        </div>

                        <div>
                            <label
                                for="sort"
                                class="mb-2 block text-sm font-medium text-text"
                            >
                                Sort by
                            </label>

                            <select
                                id="sort"
                                name="sort"
                                class="w-full rounded-xl border border-white/10 bg-surface px-3 py-2.5 text-sm text-text outline-none transition focus:border-crafthub/50 focus:ring-4 focus:ring-crafthub/5"
                            >
                                <option
                                    value="relevance"
                                    @selected (request('sort', 'relevance') === 'relevance')
                                >
                                    Relevance
                                </option>

                                <option
                                    value="downloads"
                                    @selected (request('sort') === 'downloads')
                                >
                                    Downloads
                                </option>

                                <option
                                    value="newest"
                                    @selected (request('sort') === 'newest')
                                >
                                    Date published
                                </option>

                                <option
                                    value="updated"
                                    @selected (request('sort') === 'updated')
                                >
                                    Date updated
                                </option>

                                <option
                                    value="follows"
                                    @selected (request('sort') === 'follows')
                                >
                                    Followers
                                </option>
                            </select>
                        </div>

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
                        @php
                            /** @var \App\Models\ModrinthProject $project */
                        @endphp
                        <a
                            href="{{ route('project.show', $project->slug() ?? $project->id())}}"
                            class="group block overflow-hidden rounded-2xl border border-white/10 bg-surface transition duration-200 hover:border-crafthub/30 hover:bg-surface-light/50"
                        >
                            <div class="flex gap-5 p-5 sm:p-6">
                                <div class="shrink-0">
                                    @if (!empty($project->iconUrl()))
                                        <img
                                            src="{{ $project->iconUrl() }}"
                                            alt="{{ $project->name() }}"
                                            loading="lazy"
                                            class="h-16 w-16 rounded-xl object-cover sm:h-20 sm:w-20"
                                        />

                                    @else
                                        <div
                                            class="flex h-16 w-16 items-center justify-center rounded-xl border border-white/5 bg-background sm:h-20 sm:w-20"
                                        >
                                            <img
                                                src="{{ asset('crafthub.svg') }}"
                                                alt=""
                                                class="h-8 w-8 opacity-40"
                                            />
                                        </div>

                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div
                                        class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
                                    >
                                        <div class="min-w-0">
                                            <h2
                                                class="truncate text-lg font-semibold transition group-hover:text-crafthub"
                                            >
                                                {{ $project->name() }}
                                            </h2>

                                            @if (!empty($project->author()))
                                                <p
                                                    class="mt-0.5 text-sm text-muted"
                                                >
                                                    by {{ $project->author() }}
                                                </p>

                                            @endif
                                        </div>

                                        @if (!empty($project->projectTypes()))
                                            <div
                                                class="flex shrink-0 flex-wrap justify-end gap-2"
                                            >
                                                @foreach ($project->projectTypes() as $type)
                                                    <span
                                                        class="rounded-lg border border-crafthub/15 bg-crafthub/10 px-2.5 py-1 text-xs font-medium capitalize text-crafthub"
                                                    >
                                                        {{ $type }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    @if (!empty($project->summary()))
                                        <p
                                            class="mt-3 line-clamp-2 text-sm leading-6 text-muted"
                                        >
                                            {{ $project->summary() }}
                                        </p>

                                    @endif

                                    <div
                                        class="mt-4 flex flex-wrap items-center gap-2"
                                    >
                                        <span
                                            class="inline-flex items-center gap-1.5 text-xs text-muted"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                class="h-3.5 w-3.5"
                                            >
                                                <path d="M12 3v12"></path>
                                                <path d="m7 10 5 5 5-5"></path>
                                                <path d="M5 21h14"></path>
                                            </svg>

                                            {{
                                                number_format(
                                                    $project->downloads() ?? 0,
                                                    0,
                                                    ',',
                                                    ' ',
                                                )
                                            }}
                                        </span>

                                        @foreach (array_slice($project->categories() ?? [], 0, 4) as $category)
                                            <span
                                                class="rounded-lg border border-white/5 bg-background px-2 py-1 text-xs text-muted"
                                            >
                                                {{ $category }}
                                            </span>

                                        @endforeach
                                    </div>
                                </div>

                                <div
                                    class="hidden shrink-0 items-center sm:flex"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        class="h-5 w-5 text-muted transition group-hover:translate-x-1 group-hover:text-crafthub"
                                    >
                                        <path d="m9 18 6-6-6-6"></path>
                                    </svg>
                                </div>
                            </div>
                        </a>

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
