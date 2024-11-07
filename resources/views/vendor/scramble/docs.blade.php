<x-layouts.base>

    @prepend('preload')
    <script src="https://unpkg.com/@stoplight/elements@8.3.4/web-components.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/@stoplight/elements@8.3.4/styles.min.css">

    <script>
        const originalFetch = window.fetch;

        // intercept TryIt requests and add the XSRF-TOKEN header,
        // which is necessary for Sanctum cookie-based authentication to work correctly
        window.fetch = (url, options) => {
            const CSRF_TOKEN_COOKIE_KEY = "XSRF-TOKEN";
            const CSRF_TOKEN_HEADER_KEY = "X-XSRF-TOKEN";
            const getCookieValue = (key) => {
                const cookie = document.cookie.split(';').find((cookie) => cookie.trim().startsWith(key));
                return cookie?.split("=")[1];
            };

            const updateFetchHeaders = (
                headers,
                headerKey,
                headerValue,
            ) => {
                if (headers instanceof Headers) {
                    headers.set(headerKey, headerValue);
                } else if (Array.isArray(headers)) {
                    headers.push([headerKey, headerValue]);
                } else if (headers) {
                    headers[headerKey] = headerValue;
                }
            };
            const csrfToken = getCookieValue(CSRF_TOKEN_COOKIE_KEY);
            if (csrfToken) {
                const { headers = new Headers() } = options || {};
                updateFetchHeaders(headers, CSRF_TOKEN_HEADER_KEY, decodeURIComponent(csrfToken));
                return originalFetch(url, {
                    ...options,
                    headers,
                });
            }

            return originalFetch(url, options);
        };
    </script>
    @endprepend
<elements-api
    id="docs"
    class="w-full"
    hiddenTitle
    router="hash"
/>
        @prepend('scripts')
            <script>
                (async () => {
                    const docs = document.getElementById('docs');
                    docs.apiDescriptionDocument = @json($spec);
                })();
            </script>
        @endprepend

</x-layouts.base>
