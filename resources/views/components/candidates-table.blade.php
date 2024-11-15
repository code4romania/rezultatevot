@props(['items'])

<x-table>
    <x-slot:header>
        <x-table.row>
            <x-table.th align="left" colspan="2">
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

    @foreach ($items as $item)
        <x-table.row :hiddenByDefault="$loop->index >= 5">
            <x-table.td class="w-16 ">
                <img src="{{ data_get($item, 'image') }}" alt="" class="w-10 h-10" />
            </x-table.td>

            <x-table.td align="left">
                {{ $item['name'] }}
            </x-table.td>

            <x-table.td align="right" class="w-1">
                {{ Number::format($item['votes']) }}
            </x-table.td>

            <x-table.td align="right" class="w-1">
                {{ Number::percentage($item['percent'], 2) }}
            </x-table.td>
        </x-table.row>
    @endforeach

    <x-slot:footer>
        @if ($this->aggregate->count() > 5)
            <x-table.row>
                <x-table.td align="right" colspan="4">
                    <button type="button"
                        @@click="expanded = ! expanded"
                        x-text="expanded ? @js(__('app.candidate.action.hide')) : @js(__('app.candidate.action.show'))">
                    </button>
                </x-table.td>
            </x-table.row>
        @endif
    </x-slot>

</x-table>
