@props(['article', 'expanded' => false])

<article
    wire:key="news-feed-item-{{ $article->id }}"
    x-data="{ more: @js($expanded) }"
    {{ $attributes->class('overflow-hidden bg-white rounded-lg shadow') }}>
    <div class="flex flex-col gap-4 px-4 py-5 sm:p-6">
        <header class="relative flex items-center gap-x-4">
            <img src="{{ $article->author->getFilamentAvatarUrl() }}" alt="" class="w-10 h-10 shrink-0">

            <div class="flex-1 text-sm">
                <p class="text-gray-700" rel="author">
                    {{ $article->author->name }}
                </p>
                <time
                    pubdate
                    class="text-gray-500"
                    datetime="{{ $article->published_at->toIso8601String() }}"
                    title="{{ $article->published_at->toDateTimeString() }}">
                    {{ $article->published_at->isoFormat('LLLL') }}
                </time>
            </div>

        </header>

        <div
            class="prose prose-headings:text-base prose-headings:font-medium max-w-none">
            <h1 class="m-0">{{ $article->title }}</h1>

            <div :class="{ 'line-clamp-3': !more }">
                {!! $article->content !!}

            </div>
        </div>

        @if ($article->media)
            <div class="flex flex-wrap gap-4">
                @foreach ($article->media as $media)
                    <a
                        href="{{ $media->getUrl() }}"
                        @class([
                            'flex gap-2 ',
                            $media->type === 'image'
                                ? 'shadow-sm hover:shadow-lg'
                                : 'p-2 text-sm border rounded-md drop-shadow-sm bg-gray-50 hover:bg-purple-100 max-w-48',
                        ])
                        title="{{ $media->file_name }}"
                        target="_blank"
                        rel="noopener noreferer">

                        @if ($media->type === 'image')
                            <img src="{{ $media->getUrl('large') }}"
                                alt="{{ $media->name }}"
                                class="aspect-square size-48" />
                        @else
                            <x-ri-download-2-fill class="text-gray-500 size-5 shrink-0" />

                            <span class="truncate">{{ $media->file_name }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif

    </div>

    <footer class="flex items-center justify-between gap-4 px-4 py-4 sm:px-6">
        <div class="flex items-center gap-2">
            <span class="font-medium">Share on</span>

            @php
                $url = route('front.articles.embed', $article);

                $platforms = [
                    [
                        'icon' => 'ri-facebook-fill',
                        'url' => 'https://www.facebook.com/sharer/sharer.php?u=',
                        'color' => 'bg-blue-600',
                    ],
                    [
                        'icon' => 'ri-twitter-x-fill',
                        'url' => 'https://x.com/intent/tweet?url=',
                        'color' => 'bg-gray-900',
                    ],
                ];
            @endphp

            @foreach ($platforms as $platform)
                <a href="{{ $platform['url'] . urlencode($url) }}"
                    target="_blank"
                    rel="noopener noreferer"

                    @class([
                        'rounded-md p-1.5 text-white drop-shadow-sm text-sm',
                        'border border-transparent',
                        'flex items-center justify-center',
                        'hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600',
                        $platform['color'],
                    ])>
                    <x-dynamic-component :component="$platform['icon']" class="size-5" />
                </a>
            @endforeach

            <livewire:embed-button :url="$url">
        </div>

        <button
            x-cloak
            x-show="!more"
            type="button"
            @click.prevent="more = !more"
            class="px-2.5 py-1.5 text-sm gap-x-1.5 inline-flex items-center font-semibold rounded shadow-sm text-purple-50 bg-purple-500 hover:bg-purple-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600">
            <span>{{ __('app.newsfeed.more') }}</span>

            <x-ri-arrow-down-s-line class="-me-0.5 h-5 w-5" />
        </button>
    </footer>
</article>
