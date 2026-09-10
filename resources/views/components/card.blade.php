@props (['project'])

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
                        <p class="mt-0.5 text-sm text-muted">
                            by {{ $project->author() }}
                        </p>

                    @endif
                </div>

                @if (!empty($project->projectTypes()))
                    <div class="flex shrink-0 flex-wrap justify-end gap-2">
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
                <p class="mt-3 line-clamp-2 text-sm leading-6 text-muted">
                    {{ $project->summary() }}
                </p>

            @endif

            <div class="mt-4 flex flex-wrap items-center gap-2">
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

        <div class="hidden shrink-0 items-center sm:flex">
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
