<div class="grid gap-8">
    <x-election.header :election="$election" />

    {{ $this->form }}

    @if (filled($this->aggregate))
        <div class="grid gap-2">
            <x-stacked-bar :maxItems="4" :items="$this->aggregate" />

            <ul>
                @foreach ($this->aggregate as $item)
                    <x-legend
                        :label="$item['name']"
                        :description="sprintf('%s (%s)', $item['percent'], $item['votes'])"
                        :color="$item['color']" />
                @endforeach

            </ul>
        </div>
    @endif

    <livewire:map
        :key="$this->mapKey()"
        :country="$country"
        :county="$county"
        :level="$level"
        :data="$this->data->toArray()" />

    <x-table>
        <x-slot:header>
            <x-table.row>
                <x-table.th align="left">
                    Partid / Alianță / Candidat independent
                </x-table.th>

                <x-table.th align="right">
                    Voturi
                </x-table.th>

                <x-table.th align="right">
                    %
                </x-table.th>
            </x-table.row>
        </x-slot>

        @foreach ($this->aggregate as $row)
            <x-table.row :hiddenByDefault="$loop->index >= 5">
                <x-table.td align="left">
                    {{ data_get($row, 'name', '-') }}
                </x-table.td>

                <x-table.td align="right" class="w-1">
                    {{ data_get($row, 'votes', '-') }}
                </x-table.td>

                <x-table.td align="right" class="w-1">
                    {{ data_get($row, 'percent', '-') }}
                </x-table.td>
            </x-table.row>
        @endforeach

        <x-slot:footer>
            @if ($this->aggregate->count() > 5)
                <x-table.row>
                    <x-table.td align="right" colspan="3">
                        <button type="button" @@click="expanded = ! expanded">button</button>
                    </x-table.td>
                </x-table.row>
            @endif
        </x-slot>

    </x-table>

</div>
