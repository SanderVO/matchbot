<div class="border-slate-800 rounded-md">
    <table class="table-auto w-full mb-4 bg-slate-900 border-slate-800 border-4 rounded-lg">
        <thead class="border-b-4 border-slate-800">
            <tr class="text-green-white">
                <th class="text-left p-4">
                    Name
                </th>

                <th class="text-left p-4">
                    Events
                </th>

                <th class="text-left p-4">
                    Created at
                </th>

                <th class="text-left p-4">
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse ($seasons as $season)
            <tr>
                <td class="text-left p-4">
                    {{ $season->name }}
                </td>

                <td class="text-left p-4">
                    {{ $season->events_count }}
                </td>

                <td class="text-left p-4">
                    {{ $season->created_at->format('d-m-Y, H:i') }}
                </td>

                <td>
                    @if ($season->events_count === 0)
                    <button
                        class="p-2 border rounded border-red-500 bg-red-600 hover:bg-red-500 transition-bg duration-300 ease-in-out"
                        wire:click='destroySeason({{ $season->id }})'>X</button>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td class="p-4">No seasons found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $seasons->links('pagination::tailwind') }}
</div>