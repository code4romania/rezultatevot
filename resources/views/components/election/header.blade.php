<header>
    <h1 class="text-4xl font-semibold tracking-tight text-gray-900 text-pretty sm:text-5xl">
        {{ $election->type->getLabel() }} {{ $election->year }}
    </h1>

    <span class="font-semibold text-indigo-600">
        {{ $election->title }}
    </span>
</header>

<hr>

<nav class="flex -mb-px border-b border-gray-200 gap-x-8">
    @foreach ($items as $route => $label)
        <a
            href="{{ route($route, $election) }}"
            @class([
                $isCurrent($route)
                    ? 'border-indigo-500 text-indigo-600'
                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
            ])

            wire:navigate>
            {{ $label }}
        </a>
    @endforeach
</nav>
