<x-layouts.base fixedHeight>
    <x-timeline class="overflow-y-auto" />

    <main id="content" class="flex-1 px-4 py-10 overflow-y-auto sm:px-6 lg:px-8">
        {{ $slot }}
    </main>
</x-layouts.base>
