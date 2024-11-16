<footer class="relative bg-gray-50">
    <div class="container py-12 lg:py-16">
        <nav class="grid gap-8 xl:grid-cols-3">

        </nav>

        <div class="pt-8 mt-8 border-t border-gray-200 md:flex md:items-center md:justify-between">
            <div class="flex text-gray-400 gap-x-4 md:order-2">
                <a href="https://www.linkedin.com/company/commitglobal/" target="_blank" rel="noopener noreferer"
                    class="hover:opacity-60">
                    <span class="sr-only">LinkedIn</span>
                    <x-ri-linkedin-fill class="w-5 h-5" />
                </a>

                <a href="https://www.instagram.com/commitglobal/" target="_blank" rel="noopener noreferer"
                    class="hover:opacity-60">
                    <span class="sr-only">Instagram</span>
                    <x-ri-instagram-line class="w-5 h-5" />
                </a>
                <a href="https://www.twitter.com/commitglobalorg" target="_blank" rel="noopener noreferer"
                    class="hover:opacity-60">
                    <span class="sr-only">Twitter</span>
                    <x-ri-twitter-x-fill class="w-5 h-5" />
                </a>
            </div>

            <p class="mt-8 text-base text-gray-400 md:mt-0 md:order-1">
                © {{ date('Y') }} Commit Global.
            </p>
        </div>
    </div>
</footer>
