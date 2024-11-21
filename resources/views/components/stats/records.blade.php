<section>
    <h1 class="mb-6 text-3xl font-bold">Procesul electoral</h1>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">

        @foreach ($stats as $key => $value)
            <x-stats.card>
                <dt class="text-sm font-medium leading-tight text-gray-700 truncate">
                    {{ $key }}
                </dt>
                <dd class="text-2xl font-semibold leading-tight text-gray-900">
                    {{ $value }}
                </dd>
            </x-stats.card>
        @endforeach
    </div>

</section>
