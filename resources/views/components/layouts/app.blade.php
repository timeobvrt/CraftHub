@props ([
    'title' => 'CraftHub',
    'showSearch' => false,
    'query' => '',
])

    <!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/pixel-regular.css', 'resources/css/all.css'])
</head>
<body class="min-h-screen bg-background font-sans text-text antialiased">
<div class="flex min-h-screen flex-col">
    <x-navbar :show-search="$showSearch" :query="$query" />
    <main class="flex-1">{{ $slot }}</main>
    <x-footer />
</div>
</body>
</html>
