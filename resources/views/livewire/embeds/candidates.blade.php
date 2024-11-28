<div class="grid gap-8">
    <x-election.title
        :title="__('app.navigation.turnout')"
        :level="$level"
        :country="$country"
        :county="$county"
        :locality="$locality" />

    <x-last-updated-at :$election page="turnout" class="-mt-8" />

    <x-candidates.turnouts-table :items="$this->candidates" />
</div>
