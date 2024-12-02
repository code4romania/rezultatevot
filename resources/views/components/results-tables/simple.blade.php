<x-table>
    <x-slot:header>
        <x-table.row>
            <x-table.th align="left" colspan="3">
                Candidat
            </x-table.th>

            <x-table.th align="right">
                Voturi
            </x-table.th>

            <x-table.th align="right">
                %
            </x-table.th>
        </x-table.row>
    </x-slot>

    @foreach ($votables as $votable)
        <x-table.row :hiddenByDefault="$loop->index >= 5" style="--color-custom: {{ $votable['color'] }};">
            <x-table.td class="w-16">
                <img src="{{ data_get($votable, 'image') }}" alt="" class="w-10 h-10" />
            </x-table.td>

            <x-table.td class="w-8">
                <span class="block w-4 h-4 bg-custom"></span>
            </x-table.td>

            <x-table.td align="left">
                {{ $votable['name'] }}
            </x-table.td>

            <x-table.td align="right" class="w-1">
                {{ Number::format($votable['votes']) }}
            </x-table.td>

            <x-table.td align="right" class="w-1">
                {{ Number::percentage($votable['percent'], 2) }}
            </x-table.td>
        </x-table.row>
    @endforeach

    <x-slot:footer>
        @if ($votables->count() > 5)
            <x-table.row>
                <x-table.td align="right" colspan="5">
                    <button type="button"
                        @@click="expanded = ! expanded"
                        x-text="expanded ? @js(__('app.candidate.action.hide')) : @js(__('app.candidate.action.show'))">
                    </button>
                </x-table.td>
            </x-table.row>
        @endif
    </x-slot>

</x-table>
