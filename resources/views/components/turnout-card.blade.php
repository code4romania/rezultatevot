@use('Illuminate\Support\Number')

@props(['value'])

<div class="flex gap-4">
    <x-gmdi-how-to-vote-tt class="p-2 text-white bg-purple-600 rounded-md size-12 shrink-0" />

    <div>
        <p class="text-sm font-medium leading-tight text-gray-700 truncate">
            Au votat
        </p>
        <p class="text-2xl font-semibold leading-tight text-gray-900">
            {{ Number::format($value) }}
        </p>
    </div>
</div>
