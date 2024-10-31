<div>
    <div class="mb-4">
        <select wire:model="teamId"
            class="bg-slate-900 border-slate-800 mr-0 lg:mr-4 mb-4 lg:mb-0 cursor-pointer w-100 lg:w-auto"
            wire:change.live='onTeamChange($event.target.value)'>
            <option value="0">Select team</option>
            @foreach ($teams as $team)
            <option value="{{ $team->id }}">{{ $team->name }}</option>
            @endforeach
        </select>

        <select wire:model="userId"
            class="bg-slate-900 border-slate-800 mr-0 lg:mr-4 mb-4 lg:mb-0 cursor-pointer w-100 lg:w-auto"
            wire:change.live='onUserChange($event.target.value)'>
            <option value="0">Select player</option>
            @foreach ($users as $user)
            <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>

        <select wire:model="resultType"
            class="bg-slate-900 border-slate-800 mr-0 lg:mr-4 mb-4 lg:mb-0 cursor-pointer w-100 lg:w-auto"
            wire:change.live='onResultTypeChange($event.target.value)'>
            <option value="">Select result type</option>
            <option value="maxCrawls">Max crawls</option>
            <option value="human">Human centipede</option>
            <option value="overtime">Overtime</option>
        </select>

        <select wire:model="seasonId" class="bg-slate-900 border-slate-800 cursor-pointer w-100 lg:w-auto"
            wire:change.live='onSeasonChange($event.target.value)'>
            <option value="0">Select season</option>
            @foreach ($seasons as $season)
            <option value="{{ $season->id }}">{{ $season->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="border-slate-800 rounded-md overflow-x-auto">
        <table class="table-auto w-full mb-4 bg-slate-900 border-slate-800 border-4 rounded-lg">
            <thead class="border-b-4 border-slate-800">
                <tr class="text-green-white">
                    <th class="text-left p-4">
                        Team
                    </th>

                    <th class="text-left p-4">
                        Score
                    </th>

                    <th class="text-left p-4">
                        Crawl Score
                    </th>

                    <th class="text-left p-4">
                        Type
                    </th>

                    <th class="text-left p-4">
                        Sport
                    </th>

                    <th class="text-left p-4">
                        Created at
                    </th>

                    <th class="text-left p-4">
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse ($events as $event)
                <tr class="text-white-">
                    <td class="text-left p-4">
                        <a href="/events/{{ $event->id }}">
                            <span class="{{ $event->teamResults[0]->score > $event->teamResults[1]->score ? 'font-bold text-green-500' : '' }}">
                                {{ $event->teamResults[0]->team->name }}
                            </span>
                            -
                            <span class="{{ $event->teamResults[1]->score > $event->teamResults[0]->score ? 'font-bold text-green-500' : '' }}">{{ $event->teamResults[1]->team->name }}</span>
                        </a>
                    </td>

                    <td class="text-left p-4">
                        {{ $event->teamResults[0]->score }} - {{ $event->teamResults[1]->score }}
                        @if (($event->teamResults[0]->score === 10 && $event->teamResults[1]->score === 0) || ($event->teamResults[0]->score === 0 && $event->teamResults[1]->score === 10))
                        <span title="HOPPA!">⭐</span>
                        @endif
                        @if ($event->teamResults[0]->score === 0 && $event->teamResults[1]->score === 0)
                        <span title="Invullen!">❗</span>
                        @endif
                        @if ($event->teamResults[0]->score > 10 || $event->teamResults[1]->score > 10)
                        <span title="Overtime!">🕐</span>
                        @endif
                    </td>

                    <td class="text-left p-4">
                        {{ $event->teamResults[0]->crawl_score }} - {{ $event->teamResults[1]->crawl_score }}
                        @if ($event->teamResults[0]->crawl_score > 0 && $event->teamResults[1]->crawl_score > 0)
                        <span title="Human Centipede!">🐛</span>
                        @endif
                    </td>

                    <td class="text-left p-4">
                        {{ $event->eventType->name }}
                    </td>

                    <td class="text-left p-4">
                        {{ $event->eventType->sport->name }}
                    </td>

                    <td class="text-left p-4">
                        {{ $event->created_at->format('d-m-Y, H:i') }}
                    </td>

                    <td>
                        <button
                            class="p-2 border rounded border-red-500 bg-red-600 hover:bg-red-500 transition-bg duration-300 ease-in-out"
                            wire:click='destroyEvent({{ $event->id }})'
                            wire:confirm="Are you sure you want to delete this event?">X</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="p-4">No events found</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{ $events->links() }}
    </div>
</div>