@php
    $percent = $percent();
    $width = "width: {$percent}%";
    $format;
@endphp

<div
    {{ $attributes->merge([
        'role' => 'progressbar',
        'aria-valuemin' => 0,
        'aria-valuemax' => $max,
        'aria-valuenow' => $value,
        'class' => 'flex items-center w-full h-10 gap-4',
        'style' => "--color-custom: {$color};",
    ]) }}>

    @if ($percent < 25)
        <div class="h-full bg-custom" style="{{ $width }}"></div>
        <span class="text-2xl font-bold text-custom">{{ $label() }}</span>
    @else
        <div
            class="flex items-center justify-end h-full px-1 text-xs text-center bg-custom"
            style="{{ $width }}">
            <span class="text-2xl font-bold text-white">{{ $label() }}</span>
        </div>
    @endif

</div>
