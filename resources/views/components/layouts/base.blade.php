@props([
    'timeline' => false,
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
    $timeline ? 'h-screen' : 'min-h-screen',
])
    @if ($timeline) x-data="{ sidebarOpen: false }" @endif>
    <x-site.skip-to-content />
    <x-site.banner />

    <x-site.header :$timeline />

    <div @class(['flex flex-1', 'overflow-hidden' => $timeline])>
        {{ $slot }}
    </div>

    @livewireScriptConfig
    @filamentScripts
    @vite('resources/js/app.js')
    @stack('scripts')
</body>

</html>
