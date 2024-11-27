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

<body class="flex flex-col items-center justify-center h-screen gap-8 px-6 antialiased sm:py-10 lg:px-8">

    <a href="{{ route('front.index') }}" class="block" wire:navigate>
        <div class="sr-only">{{ config('app.name') }}</div>
        <x-icon-logo class="h-14 md:h-20" />
    </a>

    <main class="max-w-lg">
        <p class="text-3xl font-extrabold text-purple-600 sm:text-4xl">
            @yield('code')
        </p>
        <h1 class="text-5xl font-semibold tracking-tight text-gray-900 text-pretty sm:text-6xl">
            @yield('title')
        </h1>
        <p class="mt-4 mb-8 text-lg font-medium text-gray-500 text-pretty sm:text-xl/8">
            @yield('message')
        </p>

        <a
            href="{{ route('front.index') }}"
            class="font-semibold text-purple-600 text-sm/7">
            <span aria-hidden="true">&larr;</span>

            <span>@lang('app.backHome')</span>
        </a>
    </main>
</body>

</html>
