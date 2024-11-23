@props([
    'timeline' => false,
    'embed' => false,
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

    <x-site.analytics />
</head>

<body @class([
    'flex flex-col font-sans antialiased',
    'h-screen' => $timeline,
    'min-h-screen' => !$timeline && !$embed,
])
    @if ($timeline) x-data="{ sidebarOpen: false }" @endif>

    @if (!$embed)
        <x-site.skip-to-content />
        <x-site.banner />

        <x-site.header :$timeline />
    @endif

    <div @class(['flex flex-1', 'overflow-hidden' => $timeline])>
        {{ $slot }}
    </div>

    @if ($embed)
        <x-site.banner embed />
    @endif

    @livewireScriptConfig
    @filamentScripts
    @vite('resources/js/app.js')
    @stack('scripts')
</body>

</html>
