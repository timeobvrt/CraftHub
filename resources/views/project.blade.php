@php
    /** @var \App\Models\ModrinthProject $project */
    $latestVersion = collect($versions)->sortByDesc('date_published')->first();

    $latestFile = $latestVersion
        ? collect($latestVersion['files'] ?? [])->firstWhere('primary', true) ??
            collect($latestVersion['files'] ?? [])->first()
        : null;
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
                                        $project->downloads() ?? 0,
                                        0,
                                        ',',
                                        ' ',
                                    )
                                }} downloads
                            </span>
                        </div>

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
                                    d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5
                                       5.5 0 0 0-7.8 7.8l1 1L12
                                       21l7.8-7.6 1-1a5.5 5.5
                                       0 0 0 0-7.8Z"
                                ></path>
                            </svg>

                            <span>
                                {{
                                    number_format(
                                        $project->followers(),
                                        0,
                                        ',',
                                        ' ',
                                    )
                                }} followers
                            </span>
                        </div>
                    </div>
                </div>
                @if ($latestVersion && $latestFile)
                    <a
                        href="{{ $latestFile['url'] }}"
                        class="mt-6 inline-flex items-center gap-2 rounded-xl bg-crafthub px-5 py-3 text-sm font-semibold text-background transition hover:bg-crafthub-light"
                    >
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

                        Download latest
                    </a>
                @endif
            </div>
        </section>

        <div class="my-10 border-t border-white/5"></div>

        <div class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_320px]">
            <article class="min-w-0">
                <div
                    class="mb-6 flex items-center gap-6 border-b border-white/5"
                >
                    <div
                        class="border-b-2 border-crafthub pb-3 text-sm font-semibold text-text"
                    >
                        Description
                    </div>
                </div>

                @if (!empty($project->description()))
                    <div
                        class="prose prose-invert prose-headings:text-text prose-p:text-muted prose-strong:text-text prose-a:text-crafthub prose-a:no-underline hover:prose-a:text-crafthub-light prose-code:text-crafthub-light prose-pre:border prose-pre:border-white/10 prose-pre:bg-surface max-w-none"
                    >
                        {!!
                            Str::markdown(
                                $project->description(),
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
            </article>

            <aside class="space-y-5">
                <div class="rounded-2xl border border-white/10 bg-surface p-5">
                    <h2 class="font-semibold">Project information</h2>

                    <div class="mt-5 space-y-4">
                        @if (!empty($project->projectTypes()))
                            <div>
                                <p
                                    class="text-xs font-medium uppercase tracking-wider text-muted"
                                >Type</p>

                                <p class="mt-1 text-sm capitalize text-text">
                                    @foreach ($project->projectTypes() as $type)
                                        {{ $type }}
                                    @endforeach
                                </p>
                            </div>
                        @endif

                        @if (!empty($project->license()))
                            <div>
                                <p
                                    class="text-xs font-medium uppercase tracking-wider text-muted"
                                >License</p>

                                <p class="mt-1 text-sm text-text">
                                    {{
                                        $project
                                            ->license()
                                            ->name()
                                    }}
                                </p>
                            </div>
                        @endif

                        @if (!empty($project->publishedAt()))
                            <div>
                                <p
                                    class="text-xs font-medium uppercase tracking-wider text-muted"
                                >Published</p>

                                <p class="mt-1 text-sm text-text">
                                    {{
                                        \Carbon\Carbon::parse(
                                            $project->publishedAt(),
                                        )->format('M j, Y')
                                    }}
                                </p>
                            </div>
                        @endif

                        @if (!empty($project->updatedAt()))
                            <div>
                                <p
                                    class="text-xs font-medium uppercase tracking-wider text-muted"
                                >Updated</p>

                                <p class="mt-1 text-sm text-text">
                                    {{
                                        \Carbon\Carbon::parse(
                                            $project->updatedAt(),
                                        )->format('M j, Y')
                                    }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                @if (!empty($project->categories()))
                    <div
                        class="rounded-2xl border border-white/10 bg-surface p-5"
                    >
                        <h2 class="font-semibold">Categories</h2>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($project->categories() as $category)
                                <span
                                    class="rounded-lg border border-white/5 bg-background px-2.5 py-1.5 text-xs capitalize text-muted"
                                >
                                    {{ $category }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="rounded-2xl border border-white/10 bg-surface p-5">
                    <div class="flex items-center justify-between gap-4">
                        <h2 class="font-semibold">Versions</h2>

                        <span class="text-xs text-muted">
                            {{
                                count(
                                    $versions,
                                )
                            }}
                        </span>
                    </div>

                    @if (!empty($versions))
                        <div class="mt-4 space-y-2">
                            @foreach (array_slice($versions, 0, 6) as $version)
                                @php
                                    $primaryFile =
                                        collect($version['files'] ?? [])->firstWhere('primary', true) ??
                                        collect($version['files'] ?? [])->first();
                                @endphp

                                <div
                                    class="rounded-xl border border-white/5 bg-background p-3.5 transition hover:border-crafthub/20"
                                >
                                    <div
                                        class="flex items-start justify-between gap-3"
                                    >
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium text-text">
                                                {{
                                                    $version[
                                                        'name'
                                                    ]
                                                }}
                                            </p>

                                            @if (!empty($version['game_versions']))
                                                <p class="mt-1 truncate text-xs text-muted">
                                                    {{
                                                        implode(
                                                            ', ',
                                                            array_slice($version['game_versions'], 0, 4),
                                                        )
                                                    }}
                                                </p>
                                            @endif
                                        </div>

                                        <div
                                            class="flex shrink-0 items-center gap-2"
                                        >
                                            @if (!empty($version['version_type']))
                                                <span
                                                    class="rounded-md bg-crafthub/10 px-2 py-1 text-[10px] font-medium uppercase text-crafthub"
                                                >
                                                    {{
                                                        $version[
                                                            'version_type'
                                                        ]
                                                    }}
                                                </span>
                                            @endif

                                            @if ($primaryFile)
                                                <a
                                                    href="{{ $primaryFile['url'] }}"
                                                    class="inline-flex items-center gap-1.5 rounded-lg bg-crafthub px-2.5 py-1.5 text-xs font-semibold text-background transition hover:bg-crafthub-light"
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
                                                </a>
                                            @endif
                                        </div>
                                    </div>

                                    @if (!empty($version['loaders']))
                                        <div
                                            class="mt-3 flex flex-wrap gap-1.5"
                                        >
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

                        @if (count($versions) > 6)
                            <button
                                type="button"
                                class="mt-4 w-full rounded-xl border border-white/10 px-4 py-2.5 text-sm font-medium text-muted transition hover:border-crafthub/30 hover:text-text"
                            >
                                View all {{ count($versions) }} versions
                            </button>
                        @endif
                    @else
                        <p class="mt-4 text-sm text-muted">No versions available.</p>
                    @endif
                </div>
            </aside>
        </div>
    </div>
</x-layouts.app>
