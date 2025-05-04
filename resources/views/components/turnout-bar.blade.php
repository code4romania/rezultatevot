@use('Filament\Support\Colors\Color')
@use('Illuminate\Support\Number')

@props(['value', 'max'])

<div class="grid gap-2">
    <div class="grid -space-y-2">
        <x-progress-bar
            :value="$max"
            :max="$max"
            :color="Color::Indigo[500]" />

        <x-progress-bar
            :value="$value"
            :max="$max"
            :color="Color::Yellow[500]"
            percent />
    </div>

    <ul>
        <x-legend
            :value="$max"
            :max="$max"
            label="Cetățeni înscriși pe liste permanente"
            :description="sprintf('%s (%s)', percent($max, $max, formatted: true), Number::format($max))"
            :color="Color::Indigo[500]" />

        <x-legend
            :value="$value"
            :max="$max"
            label="Au votat"
            :description="sprintf('%s (%s)', percent($value, $max, formatted: true), Number::format($value))"
            :color="Color::Yellow[500]" />
    </ul>
</div>
