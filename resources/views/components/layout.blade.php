<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('preload')
    @stack('scripts')

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="flex flex-col min-h-screen antialiased">
    <main id="content" class="flex-1 mb-12 lg:mb-16">
        {{ $slot }}
    </main>
</body>

</html>
