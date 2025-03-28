@use('App\Enums\ElectionType')
@use('App\Livewire\Charts')
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
            :level="$level"
            :country="$country"
            :county="$county"
            :locality="$locality" />

        @if (filled($this->aggregate))
            @if ($this->aggregate->max)
                <x-turnout-bar :value="$this->aggregate->value" :max="$this->aggregate->max" />
            @else
                <x-turnout-card :value="$this->aggregate->value" />
            @endif
        @endif

        <livewire:map
            :key="$this->componentKey('map', level: $level, county: $county)"
            :country="$country"
            :county="$county"
            :level="$level"
            :total-value="percent($this->aggregate?->value, $this->aggregate?->max)"
            :total-value-formatted="percent($this->aggregate?->value, $this->aggregate?->max, formatted: true)"
            :total-label="__('app.navigation.turnout')"
            :data="$this->data->toArray()"
            :legend="$this->getLegend()" />

        <div class="grid items-stretch gap-8 xl:grid-cols-3">
            <livewire:charts.turnout-area-chart
                :key="$this->componentKey(
                    'area-chart',
                    level: $level,
                    country: $country,
                    county: $county,
                    locality: $locality,
                )"
                :parameters="$this->getQueryParameters()"
                :election="$election"
                :areas="$this->areas" />

            <livewire:charts.turnout-population-pyramid-chart
                :key="$this->componentKey(
                    'population-chart',
                    level: $level,
                    country: $country,
                    county: $county,
                    locality: $locality,
                )"
                :parameters="$this->getQueryParameters()"
                :election="$election"
                :demographics="$this->demographics" />
        </div>

        <livewire:vote-monitor-stats
            :election="$election"
            show-embed />

        @if ($election->type->isNot(ElectionType::REFERENDUM) && !$election->has_lists)
            <x-candidates.turnouts-table :items="$this->candidates" />
        @endif

        <livewire:news-feed :election="$election" />
    </section>
</div>
