<section id="newsfeed" class="relative py-32 bg-primary-50 md:py-48">
    <div class="container max-w-6xl">
        <div
            class="prose md:prose-lg lg:prose-xl max-w-none prose-headings:font-semibold prose-a:text-primary-800 prose-a:font-medium hover:prose-a:no-underline">
            <h2 class="flex items-center gap-3 sm:gap-6">
                <span>{{ __('app.newsfeed.title') }}</span>
                <x-ri-rfid-line class="w-8 md:w-12" />
            </h2>
            <div class="mt-6 text-gray-500">
                {!! Str::markdown(__('app.newsfeed.description')) !!}
            </div>
        </div>

        <form>
            {{ $this->form }}
        </form>

        <div x-on:refresh-feed="$wire.reload()" class="relative grid gap-4 mt-10 sm:gap-8">


            {{ $this->posts->links(data: ['scrollTo' => false]) }}

            @forelse ($this->posts as $post)
                <x-news-feed-item :post="$post" />
            @empty
                <p>{{ __('app.newsfeed.empty') }}</p>
            @endforelse

            {{ $this->posts->links(data: ['scrollTo' => '#newsfeed']) }}
        </div>
    </div>

    @script
        <script>
            $wire.on('$refresh', () => {
                document.querySelector('#newsfeed').scrollIntoView()
            });
        </script>
    @endscript

</section>
