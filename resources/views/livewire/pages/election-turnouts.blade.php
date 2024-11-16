@use('Filament\Support\Colors\Color')

<div class="grid gap-8">
    <x-election.header :election="$election" page="turnout" />

    {{ $this->form }}

    <x-election.tabs
        :parameters="$this->getQueryParameters()"
        :election="$election"
        page="turnout" />

    <section class="contents">
        <x-election.title
            :title="__('app.navigation.turnout')"
            :embed-url="$this->getEmbedUrl()"
            :level="$level" />

        @if (filled($this->aggregate))
            @if ($this->aggregate->max)
                <x-turnout-bar :value="$this->aggregate->value" :max="$this->aggregate->max" />
            @else
                <x-turnout-card :value="$this->aggregate->value" />
            @endif
        @endif

        <livewire:map
            :key="$this->mapKey()"
            :country="$country"
            :county="$county"
            :level="$level"
            :data="$this->data->toArray()"
            :legend="$this->getLegend()" />

        <livewire:vote-monitor-stats :election="$election" />
    </section>
</div>
