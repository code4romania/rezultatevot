<div class="grid gap-8">
    <x-election.header :election="$election" />

    {{ $this->form }}

    <livewire:map
        :key="$this->mapKey()"
        :country="$country"
        :county="$county" />

    {{ $election }}
</div>
