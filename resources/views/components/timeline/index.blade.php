<div
    x-show="sidebarOpen"
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-gray-900/80 z-40 md:hidden"
    aria-hidden="true"
    x-cloak></div>

<div
    @keydown.window.escape="sidebarOpen = false"
    class="fixed inset-y-0 mr-16 flex flex-row-reverse w-full flex-1 z-50 items-stretch md:translate-x-0 md:relative transition ease-in-out duration-300 transform"
    x-bind:class="{
        '-translate-x-full': !sidebarOpen,
        'translate-x-0': sidebarOpen
    }">

    <div
        x-show="sidebarOpen"
        x-transition:enter="ease-in-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in-out duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @@click="sidebarOpen = false"
        class="relative flex w-16 justify-center items-start pt-5">
        <button type="button" class="-m-2.5 p-2.5" @@click="sidebarOpen = false">
            <span class="sr-only">Close sidebar</span>
            <x-ri-close-line class="w-6 h-6 text-white" />
        </button>
    </div>

    <nav
        class="flex flex-col px-6 py-8 bg-white border-r border-gray-200 sm:py-10 gap-y-5 grow flex-1 md:w-80 overflow-y-auto">
        <ul class="flex flex-col flex-1">
            @foreach ($years as $year => $electionTypes)
                <li class="relative pb-8 pl-8 group">
                    <div
                        @class([
                            'flex flex-col items-start mb-1',

                            // Vertical line
                            'group-last:before:hidden before:absolute before:left-2 before:h-full before:px-px before:bg-slate-200 before:self-start before:-translate-x-1/2 before:translate-y-3',

                            // Marker
                            'after:absolute after:left-2 after:w-3 after:h-3 after:border-2 after:box-content after:rounded-full after:-translate-x-1/2 after:translate-y-1.5',

                            $isActiveYear($year)
                                ? 'after:bg-indigo-600 after:border-indigo-50' // Active marker
                                : 'after:bg-white after:border-slate-200', // Inactive marker
                        ])>

                        <time
                            class="text-xl font-bold text-slate-900"
                            datetime="{{ $year }}">
                            {{ $year }}
                        </time>
                    </div>

                    <ul>
                        @foreach ($electionTypes as $type => $elections)
                            @if ($elections->count() === 1)
                                <x-timeline.item
                                    :isActive="$isActiveElection($elections->first())"
                                    :isLive="$elections->first()->is_live"
                                    :url="route('front.elections.results', $elections->first())"
                                    :label="$type" />
                            @else
                                <li x-data="{ open: @js($isActiveElectionType($type)) }">
                                    <button
                                        type="button"
                                        class="flex items-center justify-between"
                                        ::class="{ '-rotate-90': open }"
                                        @@click="open = !open">
                                        <span>{{ $type }}</span>

                                        <x-ri-arrow-down-s-fill
                                            class="w-4 h-4 text-slate-500"
                                            x-cloak />
                                    </button>

                                    <ul class="pl-4">
                                        @foreach ($elections as $election)
                                            <x-timeline.item
                                                :isActive="$isActiveElection($election)"
                                                :isLive="$election->is_live"
                                                :url="route('front.elections.results', $election)"
                                                :label="$election->title" />
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    </nav>
</div>
