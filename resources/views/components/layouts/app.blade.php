@props ([
    'title' => 'CraftHub',
    'showSearch' => false,
    'query' => '',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />

    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>{{ $title }}</title>

    @vite (['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-background font-sans text-text antialiased">
    <div class="flex min-h-screen flex-col">
        <x-navbar :show-search="$showSearch" :query="$query" />
        <main class="flex-1">{{ $slot }}</main>
        <x-footer />
    </div>
</body>
</html>
