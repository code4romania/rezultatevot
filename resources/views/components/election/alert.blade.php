<x-alert {{ $attributes->class('bg-red-100') }}>
    <div class="flex items-center gap-2">
        <x-heroicon-s-exclamation-triangle class="w-5 h-5 text-red-600" />

        {{ $alert }}
    </div>
</x-alert>
