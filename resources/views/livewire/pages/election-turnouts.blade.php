<div class="grid gap-8">
    <x-election.header :election="$election" />

    {{ $this->form }}

    @if (filled($this->aggregate))
        <x-progress.group :items="[
            [
                'value' => $this->aggregate->max,
                'max' => $this->aggregate->max,
                'text' => 'Cetățeni cu drept de vot',
                'percent' => false,
            ],
            [
                'value' => $this->aggregate->value,
                'max' => $this->aggregate->max,
                'text' => 'Au votat',
                'color' => 'yellow',
                'percent' => false,
            ],
        ]" />
    @endif

    <livewire:map
        :key="$this->mapKey()"
        :country="$country"
        :county="$county"
        :level="$level"
        :actionUrl="route('front.elections.turnout', $election)"
        :data="$this->data->toArray()" />

</div>
