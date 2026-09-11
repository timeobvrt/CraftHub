@props ([
    'showSearch' => false,
    'query' => '',
])

<header
    class="sticky top-0 z-50 border-b border-white/5 bg-background/85 backdrop-blur-xl"
>
    <div class="mx-auto flex h-18 max-w-7xl items-center gap-8 px-6">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center">
            <img
                src="{{ asset('crafthub-text-horizontal.svg') }}"
                alt="CraftHub"
                class="h-9 w-auto"
            />
        </a>

        @if ($showSearch)
            <form
                method="GET"
                action="{{ route('search') }}"
                class="hidden max-w-xl flex-1 md:flex"
            >
                <div
                    class="flex w-full items-center rounded-xl border border-white/10 bg-surface transition focus-within:border-crafthub/50 focus-within:ring-4 focus-within:ring-crafthub/5"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="ml-4 h-4 w-4 shrink-0 text-muted"
                    >
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </svg>

                    <input
                        type="text"
                        name="q"
                        value="{{ request('q', $query) }}"
                        placeholder="Search mods, plugins, modpacks..."
                        autocomplete="off"
                        class="min-w-0 flex-1 bg-transparent px-3 py-2.5 text-sm text-text outline-none placeholder:text-muted"
                    />
                </div>
            </form>

        @endif

        <nav class="ml-auto hidden items-center gap-1 md:flex">
            <a
                href="{{ route('home') }}"
                class="rounded-lg px-3 py-2 text-sm font-medium text-muted transition hover:bg-surface hover:text-text"
            >
                Home
            </a>

            <a
                href="{{ route('search', ['type' => 'mod']) }}"
                class="rounded-lg px-3 py-2 text-sm font-medium text-muted transition hover:bg-surface hover:text-text"
            >
                Mods
            </a>

            <a
                href="{{ route('search', ['type' => 'plugin']) }}"
                class="rounded-lg px-3 py-2 text-sm font-medium text-muted transition hover:bg-surface hover:text-text"
            >
                Plugins
            </a>

            <a
                href="{{ route('search', ['type' => 'modpack']) }}"
                class="rounded-lg px-3 py-2 text-sm font-medium text-muted transition hover:bg-surface hover:text-text"
            >
                Modpacks
            </a>
        </nav>
    </div>

    @if ($showSearch)
        <div class="border-t border-white/5 px-4 py-3 md:hidden">
            <form method="GET" action="{{ route('search') }}">
                <div
                    class="flex items-center rounded-xl border border-white/10 bg-surface"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="ml-4 h-4 w-4 text-muted"
                    >
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </svg>

                    <input
                        type="text"
                        name="q"
                        value="{{ request('q', $query) }}"
                        placeholder="Search..."
                        class="w-full bg-transparent px-3 py-3 text-sm outline-none placeholder:text-muted"
                    />
                </div>
            </form>
        </div>

    @endif
</header>
