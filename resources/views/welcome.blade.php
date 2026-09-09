<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>CraftHub</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-950 text-white">

    <header class="border-b border-white/10 bg-zinc-950/80 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
            <a href="/" class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500 font-bold text-zinc-950">
                    C
                </div>

                <span class="text-xl font-semibold tracking-tight">
                    CraftHub
                </span>
            </a>

            <nav class="flex items-center gap-6 text-sm text-zinc-400">
                <a href="/" class="transition hover:text-white">
                    Home
                </a>

                <a href="#" class="transition hover:text-white">
                    Mods
                </a>

                <a href="#" class="transition hover:text-white">
                    Plugins
                </a>

                <a href="#" class="transition hover:text-white">
                    Modpacks
                </a>
            </nav>
        </div>
    </header>

    <main>
        <section class="relative overflow-hidden">
            <div class="absolute inset-0 -z-10">
                <div class="absolute left-1/2 top-0 h-[500px] w-[900px] -translate-x-1/2 rounded-full bg-emerald-500/10 blur-3xl"></div>
            </div>

            <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32">
                <div class="mx-auto max-w-4xl text-center">
                    <h1 class="text-4xl font-bold tracking-tight sm:text-6xl">
                        All
                        <span class="text-emerald-400">
                            Minecraft
                        </span>
                        mods and plugins combined.
                    </h1>

                    <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-zinc-400">
                        Search for Minecraft mods, plugins, modpacks and other content from multiple platforms on a single page.
                    </p>

                    <form
                        action="#"
                        method="GET"
                        class="mx-auto mt-10 flex max-w-3xl flex-col gap-3 sm:flex-row"
                    >
                        <div class="flex flex-1 items-center rounded-xl border border-white/10 bg-zinc-900 px-4">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                class="h-5 w-5 text-zinc-500"
                            >
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                            </svg>

                            <input
                                type="text"
                                name="q"
                                placeholder="Search JEI, LuckPerms, Sodium..."
                                class="w-full bg-transparent px-4 py-4 text-sm text-white outline-none placeholder:text-zinc-500"
                            >
                        </div>

                        <button
                            type="submit"
                            class="rounded-xl bg-emerald-500 px-6 py-4 font-medium text-zinc-950 transition hover:bg-emerald-400"
                        >
                            Search
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!--<section class="border-y border-white/10 bg-zinc-900/40">
            <div class="mx-auto grid max-w-7xl gap-6 px-6 py-12 md:grid-cols-3">
                <div class="rounded-2xl border border-white/10 bg-zinc-900 p-6">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-400">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            class="h-6 w-6"
                        >
                            <path d="M12 2v20"></path>
                            <path d="m17 5-5-3-5 3"></path>
                            <path d="m17 19-5 3-5-3"></path>
                        </svg>
                    </div>

                    <h2 class="text-lg font-semibold">
                        Plusieurs plateformes
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-zinc-400">
                        Regroupe les projets de différentes plateformes dans une seule recherche.
                    </p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-zinc-900 p-6">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-400">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            class="h-6 w-6"
                        >
                            <path d="M4 6h16"></path>
                            <path d="M7 12h10"></path>
                            <path d="M10 18h4"></path>
                        </svg>
                    </div>

                    <h2 class="text-lg font-semibold">
                        Filtres précis
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-zinc-400">
                        Filtre par version Minecraft, loader, type de projet et plateforme.
                    </p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-zinc-900 p-6">
                    <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-400">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            class="h-6 w-6"
                        >
                            <path d="M12 3v12"></path>
                            <path d="m7 10 5 5 5-5"></path>
                            <path d="M5 21h14"></path>
                        </svg>
                    </div>

                    <h2 class="text-lg font-semibold">
                        Trouve la bonne version
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-zinc-400">
                        Affiche rapidement les versions compatibles avec ton installation Minecraft.
                    </p>
                </div>
            </div>
        </section>-->

        <section class="mx-auto max-w-7xl px-6 py-20">
            <div class="flex items-end justify-between gap-6">
                <div>
                    <p class="text-sm font-medium text-emerald-400">
                        Discover
                    </p>

                    <h2 class="mt-2 text-3xl font-bold tracking-tight">
                        Popular projects
                    </h2>

                    <p class="mt-2 text-zinc-400">
                        A selection of popular Minecraft projects.
                    </p>
                </div>

                <a
                    href="#"
                    class="hidden text-sm text-zinc-400 transition hover:text-white sm:block"
                >
                    See all →
                </a>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    [
                        'name' => 'Just Enough Items',
                        'slug' => 'JEI',
                        'type' => 'Mod',
                        'description' => 'Affiche les recettes et utilisations des objets directement en jeu.',
                        'loader' => 'Forge / NeoForge'
                    ],
                    [
                        'name' => 'LuckPerms',
                        'slug' => 'LuckPerms',
                        'type' => 'Plugin',
                        'description' => 'Un système de permissions puissant pour les serveurs Minecraft.',
                        'loader' => 'Paper / Velocity'
                    ],
                    [
                        'name' => 'Sodium',
                        'slug' => 'Sodium',
                        'type' => 'Mod',
                        'description' => 'Améliore fortement les performances de rendu côté client.',
                        'loader' => 'Fabric / NeoForge'
                    ]
                ] as $project)
                    <article class="group rounded-2xl border border-white/10 bg-zinc-900 p-6 transition hover:border-emerald-500/30 hover:bg-zinc-900/80">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wide text-emerald-400">
                                    {{ $project['type'] }}
                                </p>

                                <h3 class="mt-2 text-lg font-semibold">
                                    {{ $project['name'] }}
                                </h3>

                                <p class="mt-1 text-sm text-zinc-500">
                                    {{ $project['slug'] }}
                                </p>
                            </div>

                            <div class="rounded-lg border border-white/10 bg-zinc-950 px-3 py-1 text-xs text-zinc-400">
                                {{ $project['loader'] }}
                            </div>
                        </div>

                        <p class="mt-4 text-sm leading-6 text-zinc-400">
                            {{ $project['description'] }}
                        </p>

                        <a
                            href="#"
                            class="mt-6 inline-flex items-center text-sm font-medium text-emerald-400 transition group-hover:text-emerald-300"
                        >
                            See the project
                            <span class="ml-2">→</span>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>
    </main>

    <footer class="border-t border-white/10">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-6 py-8 text-sm text-zinc-500 sm:flex-row sm:items-center sm:justify-between">
            <p>
                CraftHub - search engine for the Minecraft ecosystem.
            </p>

            <p>
                Made with &#10084; by @timeo.bvrt
            </p>
        </div>
    </footer>

</body>
</html>
