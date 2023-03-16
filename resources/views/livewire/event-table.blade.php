<div>
    <table class="table-auto w-full border mb-4">
        <thead>
            <tr>
                <th class="text-left border-b border-slate-100 p-4">
                    ID
                </th>

                <th class="text-left border-b border-slate-100 p-4">
                    Type
                </th>

                <th class="text-left border-b border-slate-100 p-4">
                    Season
                </th>

                <th class="text-left border-b border-slate-100 p-4">
                    Aangemaakt op
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse ($events as $event)
            <tr>
                <td class="text-left border-b border-slate-100 p-4">
                    {{ $event->id }}
                </td>

                <td class="text-left border-b border-slate-100 p-4">
                    {{ $event->eventType->name }}
                </td>

                <td class="text-left border-b border-slate-100 p-4">
                    {{ $event->season->name }}
                </td>

                <td class="text-left border-b border-slate-100 p-4">
                    {{ $event->created_at }}
                </td>
            </tr>
            @empty
            <tr>
                <td class="border-b border-slate-100 p-4">No events found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $events->links('pagination::tailwind') }}
</div>