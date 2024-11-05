@props([
    'fixedHeight' => false,
])

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <x-seo::meta />
    @stack('preload')

    @livewireStyles
    @filamentStyles
    @vite('resources/css/app.css')

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <x-site.analytics />
</head>

<body @class([
    'flex flex-col  font-sans antialiased',
    $fixedHeight ? 'h-screen' : 'min-h-screen',
])>
    <x-site.skip-to-content />
    <x-site.banner />

    <x-site.header />

    <div @class(['flex flex-1', 'overflow-hidden' => $fixedHeight])>
        {{ $slot }}
    </div>

    @livewireScriptConfig
    @filamentScripts
    @vite('resources/js/app.js')
    @stack('scripts')
</body>

</html>
