@props(['post'])

<article
    wire:key="news-feed-item-{{ $post->id }}"
    x-data="{ more: false }"
    class="overflow-hidden bg-white rounded-lg shadow">
    <div class="flex flex-col gap-4 px-4 py-5 sm:p-6">
        <header class="relative flex items-center gap-x-4">
            <div class="flex-1 text-sm">
                <p class="text-gray-700" rel="author">
                    {{ $post->author->name }}
                </p>
                <time
                    pubdate
                    class="text-gray-500"
                    datetime="{{ $post->published_at->toIso8601String() }}"
                    title="{{ $post->published_at->toDateTimeString() }}">
                    {{ $post->published_at->isoFormat('LLLL') }}
                </time>
            </div>

        </header>

        <div
            class="prose prose-headings:text-base prose-headings:font-medium max-w-none">
            <h1 class="m-0">{{ $post->title }}</h1>

            <div :class="{ 'line-clamp-3': !more }">
                {!! $post->content !!}

{{--                @foreach ($post->embeds as $embed)--}}
{{--                    {!! data_get($embed, 'html', '') !!}--}}
{{--                @endforeach--}}
            </div>
        </div>

        <div class="flex flex-wrap gap-4">
            @foreach ($post->media as $media)
                <a href="{{ $media->getUrl() }}"
                    target="_blank"
                    class="shadow-sm hover:shadow-lg focus">
                    <img src="{{ $media->getUrl('thumb') }}"
                        alt="{{ $media->name }}"
                        class="aspect-square" />
                </a>
            @endforeach
        </div>
    </div>

    <footer x-show="!more" class="flex justify-end px-4 pb-4 sm:px-6">
        <button type="button"
            @click.prevent="more = !more"

            class="px-2.5 py-1.5 text-sm gap-x-1.5 inline-flex items-center font-semibold rounded shadow-sm text-primary-900 bg-secondary-500 hover:bg-secondary-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-secondary-600">
            <span>{{ __('app.newsfeed.more') }}</span>

            <x-ri-arrow-down-s-line class="-me-0.5 h-5 w-5" />
        </button>
    </footer>
</article>
