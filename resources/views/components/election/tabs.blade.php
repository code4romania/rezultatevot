<nav class="flex -mb-px border-b border-gray-200">
    @foreach ($items as $item)
        <a
            wire:navigate
            href="{{ $item['url'] }}"
            @class([
                'px-4 py-3 border-b-2 font-semibold',
                $isCurrent($item)
                    ? 'border-indigo-500 text-indigo-600 bg-indigo-50 rounded-t'
                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 hover:bg-gray-50',
            ])>
            {{ $item['label'] }}
        </a>
    @endforeach
</nav>
