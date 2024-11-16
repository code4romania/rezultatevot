<article
    class="mx-auto prose text-gray-700 md:prose-lg prose-a:text-purple-800 prose-a:font-medium hover:prose-a:no-underline prose-headings:text-purple-800 prose-h3:text-gray-700">

    <h1>{{ $page->title }}</h1>

    {!! str($page->content)->sanitizeHtml() !!}
</article>
