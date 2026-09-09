<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        {{ $project['title'] }} — CraftHub
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-zinc-950 text-white">

<header class="border-b border-white/10">
    <div class="mx-auto max-w-6xl px-6 py-4">

        <a
            href="{{ route('home') }}"
            class="text-xl font-bold"
        >
            CraftHub
        </a>

    </div>
</header>

<main class="mx-auto max-w-6xl px-6 py-12">

    <div class="flex items-start gap-6">

        @if (!empty($project['icon_url']))
            <img
                src="{{ $project['icon_url'] }}"
                alt="{{ $project['title'] }}"
                class="h-28 w-28 rounded-2xl object-cover"
            >
        @endif

        <div>

            <p class="text-sm text-emerald-400">
                {{ $project['project_type'] }}
            </p>

            <h1 class="mt-1 text-4xl font-bold">
                {{ $project['title'] }}
            </h1>

            <p class="mt-3 max-w-3xl text-zinc-400">
                {{ $project['description'] }}
            </p>

            <div class="mt-4 text-sm text-zinc-500">
                {{ number_format(
                    $project['downloads'] ?? 0,
                    0,
                    ',',
                    ' '
                ) }}
                téléchargements
            </div>

        </div>

    </div>

    <div class="mt-12 grid gap-10 lg:grid-cols-[1fr_320px]">

        <article>

            <h2 class="mb-4 text-2xl font-semibold">
                À propos
            </h2>

            <div class="whitespace-pre-line leading-7 text-zinc-300">
                {{ $project['body'] }}
            </div>

        </article>

        <aside>

            <div
                class="rounded-xl border border-white/10 bg-zinc-900 p-5"
            >

                <h2 class="font-semibold">
                    Versions
                </h2>

                <div class="mt-4 space-y-3">

                    @foreach (
                        array_slice($versions, 0, 10)
                        as $version
                    )

                        <div
                            class="rounded-lg bg-zinc-800 p-3"
                        >

                            <div class="font-medium">
                                {{ $version['name'] }}
                            </div>

                            <div class="mt-1 text-xs text-zinc-400">
                                {{ implode(
                                    ', ',
                                    $version['game_versions'] ?? []
                                ) }}
                            </div>

                        </div>

                    @endforeach

                </div>

            </div>

        </aside>

    </div>

</main>

</body>
</html>
