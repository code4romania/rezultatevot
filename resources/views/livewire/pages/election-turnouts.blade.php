@use('Filament\Support\Colors\Color')

<div class="grid gap-8">
    <x-election.header :election="$election" />

    {{ $this->form }}

    @if (filled($this->aggregate))
        <div class="grid gap-2">
            <div class="grid -space-y-2">
                <x-progress-bar
                    :value="$this->aggregate->max"
                    :max="$this->aggregate->max"
                    :color="Color::Indigo[500]" />

                <x-progress-bar
                    :value="$this->aggregate->value"
                    :max="$this->aggregate->max"
                    :color="Color::Yellow[500]"
                    percent />
            </div>

            <ul>
                <x-legend
                    :value="$this->aggregate->max"
                    :max="$this->aggregate->max"
                    label="Cetățeni cu drept de vot"
                    :description="sprintf(
                        '%s (%s)',
                        percent($this->aggregate->max, $this->aggregate->max, formatted: true),
                        $this->aggregate->max,
                    )"
                    :color="Color::Indigo[500]" />

                <x-legend
                    :value="$this->aggregate->value"
                    :max="$this->aggregate->max"
                    label="Cetățeni cu drept de vot"
                    :description="sprintf(
                        '%s (%s)',
                        percent($this->aggregate->value, $this->aggregate->max, formatted: true),
                        $this->aggregate->value,
                    )"
                    :color="Color::Yellow[500]" />
            </ul>
        </div>
    @endif

    <livewire:map
        :key="$this->mapKey()"
        :country="$country"
        :county="$county"
        :level="$level"
        :data="$this->data->toArray()" />

</div>
