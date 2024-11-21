<div class="grid gap-8">
    <x-election.title
        :title="__('app.navigation.turnout')"
        :level="$level" />

    <x-candidates.turnouts-table :items="$this->candidates" />
</div>
