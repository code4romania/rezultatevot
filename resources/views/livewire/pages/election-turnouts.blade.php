@use('Filament\Support\Colors\Color')

<div class="grid gap-8">
    <x-election.header :election="$election" page="turnout" />

    {{ $this->form }}

    <x-election.tabs
        :parameters="$this->getQueryParameters()"
        :election="$election"
        page="turnout" />

    @if (filled($this->aggregate))
        <x-turnout-bar :value="$this->aggregate->value" :max="$this->aggregate->max" />
    @endif

    <livewire:map
        :key="$this->mapKey()"
        :country="$country"
        :county="$county"
        :level="$level"
        :data="$this->data->toArray()"
        :legend="$this->getLegend()" />

</div>
