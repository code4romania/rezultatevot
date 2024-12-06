<header>
    <h1 class="text-4xl font-semibold tracking-tight text-gray-900 text-pretty sm:text-5xl">
        {{ $election->type->getLabel() }} {{ $election->year }}
    </h1>

    <span class="font-semibold text-indigo-600">
        {{ $election->title }}

        @if ($election->subtitle)
            /
            {{ $election->subtitle }}
        @endif
    </span>

    <x-election.alert :$election class="my-2" />

    <x-last-updated-at :$election :$page />
</header>
