<div class="grid gap-8">
    <x-election.title
        :title="__('app.navigation.turnout')"
        :level="$level"
        :country="$country"
        :county="$county"
        :locality="$locality" />

    <x-election.alert :$election class="-mt-4" />

    <x-last-updated-at :$election page="turnout" class="-mt-4" />

    <livewire:charts.turnout-area-chart
        :parameters="$this->getQueryParameters()"
        :election="$election"
        :areas="$this->areas" />
</div>
