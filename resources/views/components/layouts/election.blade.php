<x-layouts.base timeline>
    <x-timeline class="overflow-y-auto" />

    <div class="flex flex-col flex-1 overflow-y-auto" wire:scroll>
        <main id="content" class="flex-1 px-4 py-10 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>

        <x-site.footer />
    </div>
</x-layouts.base>
