@props(['id' => config('services.google_analytics_id')])

@if ($id)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $id }}"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', @js($id));
    </script>
@endif
