<section>
    @if ($this->count)
        <div class="flex justify-between gap-6 mb-6">
            <div>
                <h1 class="text-3xl font-bold">Observarea independentă a alegerilor</h1>
                @if (!$showEmbed)
                    <h2 class="font-medium text-gray-900 sm:text-lg">
                        {{ $election->type->getLabel() }} {{ $election->year }}
                    </h2>
                @endif
            </div>

            <livewire:embed-button :url="route('front.elections.embed.stats', $election)" />
        </div>

        <div class="prose prose-lg prose-purple">
            <p>
                Aceste date sunt colectate prin aplicația
                <a
                    href="https://www.code4.ro/ro/raport-alegeri-monitorizare-vot"
                    target="_blank"
                    rel="noopener">
                    Monitorizare Vot</a>,
                dezvoltată de
                <a
                    href="https://code4.ro"
                    target="_blank"
                    rel="noreferrer">Code for Romania</a>,
                de la observatorii independenți acreditați în secțiile de votare acoperite.
            </p>
        </div>

        <dl @class(['grid gap-5 mt-5', $this->gridColumns()])>
            @foreach ($this->stats as $stat)
                <x-stats.card @class([
                    'flex gap-4',
                    'sm:col-span-2' => $this->count > 4 && $loop->iteration <= 3,
                    'sm:col-span-3' => $this->count > 4 && $loop->iteration > 3,
                ])>

                    <div class="p-2 rounded-md bg-custom shrink-0 size-12"
                        style="--color-custom: {{ data_get($stat, 'color.500') }}">
                        <x-dynamic-component :component="$stat['icon']" class=" text-custom"
                            style="--color-custom: {{ data_get($stat, 'color.100') }}" />
                    </div>

                    <div>
                        <dt class="text-sm font-medium leading-tight text-gray-700 truncate">
                            {{ $stat['label'] }}
                        </dt>
                        <dd class="text-2xl font-semibold leading-tight text-gray-900">
                            {{ $stat['value'] }}
                        </dd>
                    </div>
                </x-stats.card>
            @endforeach
        </dl>
    @endif
</section>
