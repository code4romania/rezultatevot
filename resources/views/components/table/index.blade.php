@props(['footer'])

<div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
        <table class="min-w-full divide-y divide-gray-300" x-data="{ expanded: false }">
            <thead>
                {{ $header }}
            </thead>

            <tbody class="bg-white">
                {{ $slot }}
            </tbody>

            @if ($footer->hasActualContent())
                <tfoot>
                    {{ $footer }}
                </tfoot>
            @endif
        </table>
    </div>
</div>
