<header x-data="{ menuOpen: false }" class="relative z-40 border-b border-gray-200 shadow-sm">
    <nav class="flex justify-between gap-4 px-4 py-4">
        <a href="{{ route('front.index') }}" class="flex items-center flex-1 gap-2" wire:navigate>
            <div class="sr-only">{{ config('app.name') }}</div>
            <x-icon-logo class="h-10 md:h-14" />
        </a>

        <div class="flex items-center gap-4 sm:relative">
            <div class="items-center hidden gap-2 md:flex">
                @foreach ($menuItems as $item)
                    <x-navigation-item :item="$item" />
                @endforeach
            </div>

            @if ($timeline)
                <button
                    type="button"
                    @@click="sidebarOpen = !sidebarOpen"
                    class="p-2 -m-2 text-gray-700 md:hidden">
                    <span class="sr-only">Open sidebar</span>
                    <x-ri-history-line class="w-5 h-5" />
                </button>
            @endif

            <button
                type="button"
                @@click="menuOpen = !menuOpen"
                class="p-2 -m-2 text-gray-700 md:hidden">
                <div class="sr-only">{{ __('app.menu') }}</div>
                <x-ri-menu-line x-show="!menuOpen" class="w-5 h-5" />
                <x-ri-close-line x-show="menuOpen" class="w-5 h-5" x-cloak />
            </button>
        </div>

        <div class="absolute inset-x-0 z-50 transition origin-top transform bg-white shadow-lg top-full md:hidden"
            x-show="menuOpen" x-collapse x-cloak>
            <ul class="container flex flex-col py-4 text-gray-600 gap-y-1 md:py-8">
                @foreach ($menuItems as $item)
                    <li><x-navigation-item mobile :item="$item" /></li>
                @endforeach
            </ul>
        </div>
    </nav>
</header>
