<div x-data="embed">
    <button
        type="button"
        class="p-2 text-sm border rounded-md drop-shadow-sm bg-gray-50"
        :class="{
            'bg-green-50 text-green-600': isSuccesful,
            'hover:bg-purple-100': !isSuccesful
        }"
        @@click="copy">

        <x-ri-code-s-slash-fill class="w-4 h-4" x-show="!isSuccesful" />
        <x-ri-check-line class="w-4 h-4" x-show="isSuccesful" x-cloak />
    </button>
</div>
