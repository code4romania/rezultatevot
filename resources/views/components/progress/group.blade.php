<div>
    @foreach ($items as $item)
        <x-progress.bar
            :value="data_get($item, 'value')"
            :max="data_get($item, 'max')"
            :color="data_get($item, 'color', 'indigo')"
            :percent="data_get($item, 'percent', false)" />
    @endforeach

    <ul>
        @foreach ($items as $item)
            <x-progress.legend
                :value="data_get($item, 'value')"
                :text="data_get($item, 'text')"
                :max="data_get($item, 'max')"
                :color="data_get($item, 'color', 'indigo')"
                :percent="data_get($item, 'percent', false)" />
        @endforeach
    </ul>
</div>
