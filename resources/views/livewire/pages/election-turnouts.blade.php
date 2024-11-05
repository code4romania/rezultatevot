<div class="grid gap-8">
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
                'percent' => true,
            ],
        ]" />
    @endif

    <livewire:map
        :key="$this->mapKey()"
        :country="$country"
        :county="$county" />

    @dump($election)
</div>
