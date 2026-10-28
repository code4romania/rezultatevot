@push('scripts')
    @vite('resources/js/iframe.js')
@endpush

<x-layouts.base embed>
    <main id="content" class="flex-1 px-4 py-10 overflow-y-auto sm:px-6 lg:px-8" wire:scroll>
        {{ $slot }}
    </main>
</x-layouts.base>
