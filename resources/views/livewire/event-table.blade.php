<div class="border-slate-800 rounded-md">
    <table class="table-auto w-full mb-4 bg-slate-900 border-slate-800 border-4 rounded-lg">
        <thead class="border-b-4 border-slate-800">
            <tr class="text-green-white">
                <th class="text-left p-4 color-gray">
                    ID
                </th>

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
                    Season
                </th>

                <th class="text-left p-4">
                    Created at
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse ($events as $event)
            <tr class="text-white-">
                <td class="text-left p-4 ">
                    {{ $event->id }}
                </td>

                <td class="text-left p-4">
                    {{ $event->teamResults[0]->team->name }} - {{ $event->teamResults[1]->team->name }}
                </td>

                <td class="text-left p-4">
                    {{ $event->teamResults[0]->score }} - {{ $event->teamResults[1]->score }}
                </td>

                <td class="text-left p-4">
                    {{ $event->teamResults[0]->crawl_score }} - {{ $event->teamResults[1]->crawl_score }}
                </td>

                <td class="text-left p-4">
                    {{ $event->eventType->name }}
                </td>

                <td class="text-left p-4">
                    {{ $event->eventType->sport->name }}
                </td>

                <td class="text-left p-4">
                    {{ $event->season->name }}
                </td>

                <td class="text-left p-4">
                    {{ $event->created_at->format('d-m-Y, H:i') }}
                </td>
            </tr>
            @empty
            <tr>
                <td class="p-4">No events found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $events->links('pagination::tailwind') }}
</div>