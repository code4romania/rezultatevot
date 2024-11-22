@props(['title', 'url'])

<div class="flex items-center justify-between gap-4">
    <span>{{ $title }}</span>

    <livewire:embed-button :url="$url" />
</div>
