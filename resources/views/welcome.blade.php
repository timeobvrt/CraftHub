<x-layouts.app title="CraftHub">
    <main>
        <section class="relative overflow-hidden border-b border-white/5">
            <div class="pointer-events-none absolute inset-0 -z-10">
                <div
                    class="absolute left-1/2 -top-55 h-150 w-255 -translate-x-1/2 rounded-full bg-crafthub/10 blur-[130px]"
                ></div>
            </div>

            <div class="mx-auto max-w-7xl px-6 py-28 sm:py-36 lg:py-44">
                <div class="mx-auto max-w-4xl text-center">
                    <h1
                        class="text-balance text-5xl font-bold tracking-[-0.04em] sm:text-6xl lg:text-7xl"
                    >
                        All
                        <span class="text-crafthub"> Minecraft </span>
                        projects combined.
                    </h1>

                    <p class="mt-6 text-balance text-lg leading-6 text-muted">Search mods, plugins and modpacks from multiple platforms without jumping between websites.</p>
                    <form
                        action="{{ route('search') }}"
                        method="GET"
                        class="mx-auto mt-10 max-w-3xl"
                    >
                        <div
                            class="group flex items-center rounded-2xl border border-white/10 bg-surface p-2 shadow-2xl shadow-black/20 transition focus-within:border-crafthub/50 focus-within:ring-1 focus-within:ring-crafthub/50"
                        >
                            <div class="flex flex-1 items-center">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    class="ml-3 h-5 w-5 shrink-0 text-muted"
                                >
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.3-4.3"></path>
                                </svg>

                                <input
                                    type="text"
                                    name="q"
                                    placeholder="Search JEI, LuckPerms, Sodium..."
                                    autocomplete="off"
                                    class="min-w-0 flex-1 bg-transparent px-4 py-3 text-sm text-text outline-none placeholder:text-muted"
                                />
                            </div>

                            <button
                                type="submit"
                                class="rounded-xl bg-crafthub px-6 py-3 text-sm font-semibold text-background transition hover:bg-crafthub-light"
                            >
                                Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <section class="border-b border-white/5">
            <div
                class="mx-auto grid max-w-7xl gap-px bg-white/5 md:grid-cols-1"
            >
                <article class="bg-background px-8 py-10">
                    <div
                        class="mb-5 flex h-11 w-11 items-center justify-center rounded-xl bg-crafthub/10 text-crafthub"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            class="h-5 w-5"
                        >
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21h-4v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9A1.7 1.7 0 0 0 3 14H3v-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7 7 4.2l.1.1A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.6V3h4v.1a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.1v4H21a1.7 1.7 0 0 0-1.6 1Z"></path>
                        </svg>
                    </div>

                    <h2 class="text-lg font-semibold">Multiple platforms</h2>

                    <p class="mt-2 text-sm leading-6 text-muted">Search content from different Minecraft platforms like Modrinth, CurseForge, Hangar, Spigot, Bukkit</p>
                </article>
            </div>
        </section>
    </main>
</x-layouts.app>
