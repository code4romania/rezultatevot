<section id="newsfeed">
    @if ($this->articles->count())
        <div class="relative px-4 py-10 -mx-4 bg-gray-50 sm:px-6 lg:px-8 sm:-mx-6 lg:-mx-8">
            <div class="container max-w-6xl">
                <div class="flex items-center gap-3 mb-6 sm:gap-6">
                    <x-ri-rfid-line class="w-8 md:w-12" />
                    <h1 class="text-3xl font-bold">{{ __('app.newsfeed.title') }}</h1>
                </div>

                <div class="prose prose-lg prose-purple">
                    <p>{{ __('app.newsfeed.description') }}</p>
                </div>

                <div x-on:refresh-feed="$wire.reload()" class="relative grid gap-4 mt-10 sm:gap-8">
                    {{ $this->articles->links(data: ['scrollTo' => false]) }}
                    @foreach ($this->articles as $article)
                        <x-news-feed-item :article="$article" />
                    @endforeach

                    {{ $this->articles->links(data: ['scrollTo' => '#newsfeed']) }}
                </div>
            </div>

            @script
                <script>
                    $wire.on('$refresh', () => {
                        document.querySelector('#newsfeed').scrollIntoView()
                    });
                </script>
            @endscript
        </div>
    @endif
</section>
