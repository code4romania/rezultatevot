<header>
    <h1 class="text-4xl font-semibold tracking-tight text-gray-900 text-pretty sm:text-5xl">
        {{ $election->type->getLabel() }} {{ $election->year }}
    </h1>

    <span class="font-semibold text-indigo-600">
        {{ $election->title }}
    </span>
</header>
