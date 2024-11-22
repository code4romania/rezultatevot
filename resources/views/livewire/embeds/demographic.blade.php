<div class="grid gap-8">
    <x-election.title
        :title="__('app.navigation.turnout')"
        :level="$level"
        :country="$country"
        :county="$county"
        :locality="$locality" />

    <livewire:charts.turnout-population-pyramid-chart
        :parameters="$this->getQueryParameters()"
        :election="$election"
        :demographics="$this->demographics" />
</div>
