<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Recherche {{ $query }} — CraftHub
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-zinc-950 text-white">

<header class="border-b border-white/10">
    <div class="mx-auto flex max-w-7xl items-center gap-8 px-6 py-4">

        <a
            href="{{ route('home') }}"
            class="text-xl font-bold"
        >
            CraftHub
        </a>

        <form
            method="GET"
            action="{{ route('search') }}"
            class="flex flex-1 gap-2"
        >
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Rechercher..."
                class="flex-1 rounded-lg border border-white/10 bg-zinc-900 px-4 py-2 outline-none focus:border-emerald-500"
            >

            <button
                class="rounded-lg bg-emerald-500 px-5 py-2 font-medium text-zinc-950"
            >
                Rechercher
            </button>
        </form>

    </div>
</header>

<main class="mx-auto max-w-7xl px-6 py-10">

    <div class="grid gap-8 lg:grid-cols-[220px_1fr]">

        <aside>

            <form
                method="GET"
                action="{{ route('search') }}"
                class="space-y-5"
            >

                <input
                    type="hidden"
                    name="q"
                    value="{{ request('q') }}"
                >

                <div>
                    <label
                        for="version"
                        class="mb-2 block text-sm font-medium"
                    >
                        Version Minecraft
                    </label>

                    <select
                        id="version"
                        name="version"
                        class="w-full rounded-lg border border-white/10 bg-zinc-900 px-3 py-2"
                    >
                        <option value="">
                            Toutes
                        </option>

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
                        ] as $version)

                            <option
                                value="{{ $version }}"
                                @selected(request('version') === $version)
                            >
                                {{ $version }}
                            </option>

                        @endforeach
                    </select>
                </div>

                <div>
                    <label
                        for="loader"
                        class="mb-2 block text-sm font-medium"
                    >
                        Loader
                    </label>

                    <select
                        id="loader"
                        name="loader"
                        class="w-full rounded-lg border border-white/10 bg-zinc-900 px-3 py-2"
                    >
                        <option value="">
                            Tous
                        </option>

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
                        ] as $loader)

                            <option
                                value="{{ $loader }}"
                                @selected(request('loader') === $loader)
                            >
                                {{ ucfirst($loader) }}
                            </option>

                        @endforeach
                    </select>
                </div>

                <div>
                    <label
                        for="type"
                        class="mb-2 block text-sm font-medium"
                    >
                        Type
                    </label>

                    <select
                        id="type"
                        name="type"
                        class="w-full rounded-lg border border-white/10 bg-zinc-900 px-3 py-2"
                    >
                        <option value="">
                            Tous
                        </option>

                        <option
                            value="mod"
                            @selected(request('type') === 'mod')
                        >
                            Mod
                        </option>

                        <option
                            value="modpack"
                            @selected(request('type') === 'modpack')
                        >
                            Modpack
                        </option>

                        <option
                            value="resourcepack"
                            @selected(request('type') === 'resourcepack')
                        >
                            Resource Pack
                        </option>

                        <option
                            value="shader"
                            @selected(request('type') === 'shader')
                        >
                            Shader
                        </option>
                    </select>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-emerald-500 px-4 py-2 font-medium text-zinc-950"
                >
                    Appliquer
                </button>

            </form>

        </aside>

        <section>

            <div class="mb-6">
                <h1 class="text-2xl font-bold">
                    Résultats pour
                    “{{ $query }}”
                </h1>

                <p class="mt-1 text-sm text-zinc-400">
                    {{ number_format($total, 0, ',', ' ') }}
                    résultats trouvés
                </p>
            </div>

            <div class="space-y-4">

                @forelse ($projects as $project)

                    <a
                        href="{{ route('project.show', $project['slug'] ?? $project['project_id']) }}"
                        class="block rounded-xl border border-white/10 bg-zinc-900 p-5 transition hover:border-emerald-500/40"
                    >

                        <div class="flex gap-4">

                            @if (!empty($project['icon_url']))
                                <img
                                    src="{{ $project['icon_url'] }}"
                                    alt=""
                                    class="h-16 w-16 rounded-xl object-cover"
                                >
                            @else
                                <div
                                    class="h-16 w-16 rounded-xl bg-zinc-800"
                                ></div>
                            @endif

                            <div class="min-w-0 flex-1">

                                <div class="flex items-start justify-between gap-4">

                                    <div>
                                        <h2 class="text-lg font-semibold">
                                            {{ $project['title'] }}
                                        </h2>

                                        <p class="text-sm text-zinc-500">
                                            par {{ $project['author'] }}
                                        </p>
                                    </div>

                                    <span
                                        class="rounded-lg bg-emerald-500/10 px-3 py-1 text-xs text-emerald-400"
                                    >
                                        {{ $project['project_type'] }}
                                    </span>

                                </div>

                                <p class="mt-3 text-sm text-zinc-400">
                                    {{ $project['description'] }}
                                </p>

                                <div
                                    class="mt-4 flex flex-wrap gap-2 text-xs text-zinc-400"
                                >

                                    <span>
                                        ↓
                                        {{ number_format(
                                            $project['downloads'] ?? 0,
                                            0,
                                            ',',
                                            ' '
                                        ) }}
                                    </span>

                                    @foreach (
                                        array_slice(
                                            $project['display_categories'] ?? [],
                                            0,
                                            4
                                        )
                                        as $category
                                    )

                                        <span
                                            class="rounded-md bg-zinc-800 px-2 py-1"
                                        >
                                            {{ $category }}
                                        </span>

                                    @endforeach

                                </div>

                            </div>

                        </div>

                    </a>

                @empty

                    <div
                        class="rounded-xl border border-white/10 bg-zinc-900 p-10 text-center"
                    >
                        <p class="text-zinc-400">
                            Aucun projet trouvé.
                        </p>
                    </div>

                @endforelse

            </div>

        </section>

    </div>

</main>

</body>
</html>
