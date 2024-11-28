<div class="grid gap-2">
    <x-last-updated-at :$election page="turnout" class="justify-end" />

    <livewire:charts.top-counties-chart :election="$election" />
</div>
