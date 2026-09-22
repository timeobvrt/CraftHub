<x-layouts.app title="CraftHub">
    <main>
        <section class="relative overflow-hidden">
            <div class="relative mx-auto max-w-7xl px-6 py-28 sm:py-36 lg:py-44">
                <div class="mx-auto max-w-4xl text-center">
                    <div class="mb-10 inline-flex items-center">
                        <img
                            src="{{ asset('crafthub.svg') }}"
                            alt="CraftHub"
                            class="h-20 w-20"
                        />
                    </div>

                    <h1 class="text-balance text-5xl font-bold tracking-tighter sm:text-6xl lg:text-7xl">
                        All
                        <span class="text-crafthub font-pixel"> Minecraft </span>
                        projects combined.
                    </h1>

                    <form
                        action="{{ route('search') }}"
                        method="GET"
                        class="mx-auto mt-10 max-w-3xl"
                    >
                        <div class="pixel-border-green flex items-center bg-surface p-2">
                            <div class="flex min-w-0 flex-1 items-center">
                                <input
                                    type="text"
                                    name="q"
                                    placeholder="Search JEI, LuckPerms, Sodium..."
                                    autocomplete="off"
                                    class="min-w-0 flex-1 bg-transparent px-4 py-3 text-sm text-text outline-none placeholder:text-muted"
                                />
                            </div>

                            <x-button type="submit">
                                <i class="fa-pixel fa-regular fa-magnifying-glass"></i>
                                Search
                            </x-button>
                        </div>
                    </form>

                </div>
            </div>
        </section>

    </main>
</x-layouts.app>
