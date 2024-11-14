@props(['embed' => false])

<aside class="bg-gray-100">
    <div class="flex gap-4 px-4 py-3">
        @if ($embed)
            <a href="{{ route('front.index') }}" class="flex items-center" wire:navigate>
                <div class="sr-only">{{ config('app.name') }}</div>
                <x-icon-logo class="h-10 md:h-14" />
            </a>
        @endif

        <a
            href="https://www.commitglobal.org"
            target="_blank"
            rel="noopener"
            class="flex items-center w-full max-w-2xl gap-3 text-sm hover:text-blue-600 focus:text-blue-600 focus:outline-0 hover:underline focus:underline">
            <x-icon-commitglobal class="h-6 sm:h-8 shrink-0" />

            <span>{{ __('app.banner') }}</span>
        </a>
    </div>
</aside>
