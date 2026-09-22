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
    class="group relative block border border-white/10 bg-surface
    transition-all duration-100
    hover:border-crafthub/40 hover:bg-surface-light"
>
    <div class="flex gap-4 p-4 sm:gap-5 sm:p-5">

        <div class="shrink-0">
            @if ($project->iconUrl())
                <img
                    src="{{ $project->iconUrl() }}"
                    alt="{{ $project->name() }}"
                    loading="lazy"
                    class="h-16 w-16 object-cover
                    sm:h-18 sm:w-18"
                />
            @else
                <div
                    class="flex h-16 w-16 items-center justify-center
                    border border-white/10 bg-background
                    sm:h-18 sm:w-18"
                >
                    <img
                        src="{{ asset('crafthub.svg') }}"
                        alt=""
                        class="h-7 w-7 opacity-20"
                    />
                </div>
            @endif
        </div>

        <div class="min-w-0 flex-1">

            <div class="flex items-start gap-3">

                <div class="min-w-0 flex-1">
                    <h2
                        class="truncate text-base font-semibold text-text
                        transition-colors group-hover:text-crafthub sm:text-lg"
                    >
                        {{ $project->name() }}
                    </h2>

                    @if ($project->author())
                        <p class="mt-0.5 truncate text-xs text-muted">
                            by {{ $project->author() }}
                        </p>
                    @endif
                </div>

                @if (!empty($project->projectTypes()))
                    <div class="hidden shrink-0 items-center gap-1.5 sm:flex">
                        @foreach (array_slice($project->projectTypes(), 0, 2) as $type)
                            <span
                                class="truncate text-xs text-muted/70"
                            >
                                {{ $type }}
                            </span>

                            @if (!$loop->last)
                                <span class="text-white/15">/</span>
                            @endif
                        @endforeach
                    </div>
                @endif

            </div>

            @if ($project->summary())
                <p
                    class="mt-3 line-clamp-2 max-w-3xl text-sm leading-5 text-muted"
                >
                    {{ $project->summary() }}
                </p>
            @endif

            <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2">

                <span class="inline-flex items-center gap-1.5 text-xs text-muted">
                    <i class="fa-pixel fa-regular fa-arrow-down-to-bracket h-3.5 w-3.5"></i>

                    {{ number_format($project->downloads(), 0, ',', ' ') }}
                </span>

                @if (!empty($project->projectTypes()))
                    <div class="flex min-w-0 items-center gap-2 overflow-hidden">
                        @foreach ($project->categories() as $category)
                            <span
                                class="truncate text-xs text-muted/70"
                            >
                                {{ $category }}
                            </span>

                            @if (!$loop->last)
                                <span class="text-white/15">/</span>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div
            class="hidden shrink-0 items-center text-muted/20
            transition-all duration-100
            group-hover:translate-x-1 group-hover:text-crafthub
            sm:flex"
        >
            <i class="fa-pixel fa-regular fa-arrow-right h-5 w-5"></i>
        </div>

    </div>
</a>
