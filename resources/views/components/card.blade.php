@php use App\Models\Project; @endphp
@props(['project'])

@php
    /** @var Project $project */

    $sources = collect($project->sources());

    $primarySource = $sources->firstWhere('platform', 'modrinth')
        ?? $sources->first();

    $additionalSources = $sources
        ->reject(
            fn(array $source) =>
                ($source['platform'] ?? null)
                === ($primarySource['platform'] ?? null),
        )
        ->mapWithKeys(
            fn(array $source) => [
                $source['platform'] => (string) $source['id'],
            ],
        )
        ->all();

    $href = route('project.show', [
        'provider' => $primarySource['platform'],
        'id' => $primarySource['slug'] ?? $primarySource['id'],
        'sources' => $additionalSources,
    ]);
@endphp

<a
    href="{{ $href }}"
    rel="noopener noreferrer"
    class="group block overflow-hidden rounded-2xl border border-white/[0.07] bg-surface transition-all duration-200 hover:border-white/[0.14] hover:bg-surface-light"
>
    <div class="flex gap-4 p-4 sm:p-5">
        <div class="shrink-0">
            @if ($project->iconUrl())
                <img
                    src="{{ $project->iconUrl() }}"
                    alt="{{ $project->name() }}"
                    loading="lazy"
                    class="h-16 w-16 rounded-xl object-cover shadow-sm ring-1 ring-black/10 sm:h-18 sm:w-18"
                />
            @else
                <div
                    class="flex h-16 w-16 items-center justify-center rounded-xl bg-background ring-1 ring-white/6 sm:h-18 sm:w-18"
                >
                    <img
                        src="{{ asset('crafthub.svg') }}"
                        alt=""
                        class="h-8 w-8 opacity-30"
                    />
                </div>
            @endif
        </div>

        <div class="min-w-0 flex-1">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h2
                        class="truncate text-base font-semibold text-text transition-colors group-hover:text-crafthub sm:text-lg"
                    >
                        {{ $project->name() }}
                    </h2>

                    @if ($project->author())
                        <p class="mt-0.5 truncate text-sm text-muted">
                            {{ $project->author() }}
                        </p>
                    @endif
                </div>

                @if (!empty($project->projectTypes()))
                    <div class="hidden shrink-0 items-center gap-1.5 sm:flex">
                        @foreach (array_slice($project->projectTypes(), 0, 2) as $type)
                            <span
                                class="text-xs capitalize text-muted"
                            >
                                {{ $type }}
                            </span>

                            @if (!$loop->last)
                                <span class="text-white/20">·</span>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            @if ($project->summary())
                <p class="mt-2 line-clamp-2 max-w-3xl text-sm leading-5 text-muted">
                    {{ $project->summary() }}
                </p>
            @endif

            <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2">
                <span class="inline-flex items-center gap-1.5 text-xs text-muted">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="h-3.5 w-3.5"
                    >
                        <path d="M12 3v12" />
                        <path d="m7 10 5 5 5-5" />
                        <path d="M5 21h14" />
                    </svg>

                    {{ number_format($project->downloads(), 0, ',', ' ') }}
                </span>

                @if (!empty($project->categories()))
                    <div class="flex min-w-0 items-center gap-2 overflow-hidden">
                        @foreach (array_slice($project->categories(), 0, 3) as $category)
                            <span class="truncate text-xs text-muted/80">
                                {{ $category }}
                            </span>

                            @if (!$loop->last)
                                <span class="text-white/15">·</span>
                            @endif
                        @endforeach
                    </div>
                @endif

                @if ($sources->isNotEmpty())
                    <div class="ml-auto flex items-center gap-1.5">
                        @if ($sources->isNotEmpty())
                            <div class="ml-auto flex items-center gap-1.5 text-xs text-muted/70">
                                @foreach ($sources as $source)
                                    <span>
                                        {{ ucfirst($source['platform'] ?? '') }}
                                    </span>
                                    @if (!$loop->last)
                                        <span class="text-white/15">·</span>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div
            class="hidden shrink-0 items-center text-muted/30 transition-all group-hover:translate-x-0.5 group-hover:text-crafthub sm:flex"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="h-5 w-5"
            >
                <path d="m9 18 6-6-6-6" />
            </svg>
        </div>
    </div>
</a>
