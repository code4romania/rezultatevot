<div {{ $attributes->class(['flex gap-1 text-sm']) }}>
    <span class="text-gray-700">Ultima actualizare:</span>
    <time
        class="font-medium text-gray-900"
        datetime="{{ $timestamp->toIso8601String() }}">
        {{ $timestamp->toDateTimeString() }}
    </time>
    <span>ora României</span>
</div>
