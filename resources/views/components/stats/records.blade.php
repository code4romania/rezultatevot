<section>
    <h1 class="mb-6 text-3xl font-bold">Procesul electoral</h1>

    <div class="grid gap-4 sm:grid-cols-2">

        @foreach ($stats as $key => $value)
            <x-stats.card>
                <dt class="text-sm font-medium leading-tight text-gray-700">
                    {{ $key }}
                </dt>
                <dd class="text-2xl font-semibold leading-tight text-gray-900">
                    {{ $value }}
                </dd>
            </x-stats.card>
        @endforeach
    </div>
</section>
