<footer class="relative bg-gray-50">
    <div class="container py-12 lg:py-16">
        <nav class="flex flex-wrap gap-x-8 gap-y-3 text-sm/6">
            @foreach ($menuItems as $item)
                <x-navigation-item :item="$item" />
            @endforeach
        </nav>

        <div class="pt-8 mt-8 border-t border-gray-200 md:flex md:items-center md:justify-between">
            <div class="flex text-gray-400 gap-x-4 md:order-2">
                @foreach ($socialItems as $item)
                    <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferer"
                        class="hover:opacity-60">
                        <span class="sr-only">{{ $item['name'] }}</span>
                        <x-dynamic-component :component="$item['icon']" class="w-5 h-5" />
                    </a>
                @endforeach
            </div>

            <p class="mt-8 text-base text-gray-400 md:mt-0 md:order-1">
                © {{ date('Y') }} Code for Romania.
            </p>
        </div>
    </div>
</footer>
