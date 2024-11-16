<div class="flex justify-between">
    <div class="">
        <h1 class="text-xl font-semibold text-gray-900 sm:text-2xl">{{ $title }}</h1>
        <h2 class="font-medium text-gray-900 sm:text-lg">{{ $level->getLabel() }}</h2>
    </div>

    @if ($embedUrl)
        <livewire:embed-button :url="$embedUrl" :key="$embedKey()" />
    @endif
</div>
