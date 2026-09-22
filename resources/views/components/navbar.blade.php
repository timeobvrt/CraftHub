@props([
    'showSearch' => false,
    'query' => '',
])

<header class="sticky top-0 z-50 border-b border-white/10 bg-background/95 backdrop-blur-md">
    <div class="mx-auto flex h-16 max-w-7xl items-center gap-6 px-4 sm:px-6 justify-between">
        <a
            href="{{ route('home') }}"
            class="group flex shrink-0 items-center"
        >
            <img
                src="{{ asset('crafthub-text-horizontal.svg') }}"
                alt="CraftHub"
                class="h-8 w-auto"
            />
        </a>

        @if ($showSearch)
            <form
                method="GET"
                action="{{ route('search') }}"
                class="hidden max-w-lg flex-1 md:flex"
            >
                <div
                    class="flex w-full items-center border border-white/10 bg-surface
                    shadow-[2px_2px_0_rgba(0,0,0,0.35)]
                    transition-colors
                    focus-within:border-crafthub/50"
                >
                    <i class="fa-pixel fa-regular fa-search ml-3 h-4 w-4 shrink-0 text-muted"></i>

                    <input
                        type="text"
                        name="q"
                        value="{{ request('q', $query) }}"
                        placeholder="Search mods, plugins..."
                        autocomplete="off"
                        class="min-w-0 flex-1 bg-transparent px-3 py-2.5 text-sm outline-none placeholder:text-muted font-pixel"
                    />
                </div>
            </form>
        @endif

        <nav class="ml-auto hidden items-center gap-1 sm:flex">

            <a
                href="{{ route('home') }}"
                class="group flex items-center gap-2 border border-transparent
                px-3 py-2 text-sm text-muted transition
                hover:border-white/10 hover:bg-surface hover:text-text"
            >
                Home
            </a>

            <a
                href="{{ route('search', ['type' => 'mod']) }}"
                class="group flex items-center gap-2 border border-transparent
                px-3 py-2 text-sm text-muted transition
                hover:border-crafthub/30 hover:bg-surface hover:text-crafthub"
            >
                Mods
            </a>

            <a
                href="{{ route('search', ['type' => 'plugin']) }}"
                class="group flex items-center gap-2 border border-transparent
                px-3 py-2 text-sm text-muted transition
                hover:border-crafthub/30 hover:bg-surface hover:text-crafthub"
            >
                Plugins
            </a>
        </nav>

        <button
            type="button"
            class="flex h-9 w-9 items-center justify-center border
            border-white/10 bg-surface text-muted transition
            hover:border-crafthub/40 hover:text-crafthub sm:hidden"
            aria-label="Open menu"
        >
            <i class="fa-pixel fa-regular fa-bars h-4 w-4"></i>
        </button>
    </div>

    @if ($showSearch)
        <div class="border-t border-white/5 px-4 py-3 sm:hidden">
            <form method="GET" action="{{ route('search') }}">
                <div
                    class="flex items-center border border-white/10 bg-surface
                    focus-within:border-crafthub/50"
                >
                    <i class="fa-pixel fa-regular fa-search ml-3 h-4 w-4 text-muted"></i>

                    <input
                        type="text"
                        name="q"
                        value="{{ request('q', $query) }}"
                        placeholder="Search..."
                        class="w-full bg-transparent px-3 py-2.5 text-sm outline-none placeholder:text-muted font-pixel"
                    />
                </div>
            </form>
        </div>
    @endif
</header>
