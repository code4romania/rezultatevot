<nav class="flex flex-col px-6 pb-4  bg-white border-r border-gray-200 sm:py-10 gap-y-5 w-80">
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
