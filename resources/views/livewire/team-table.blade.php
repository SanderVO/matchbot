<div class="border-slate-800 rounded-md">
    <table class="table-auto w-full mb-4 bg-slate-900 border-slate-800 border-4 rounded-lg">
        <thead class="border-b-4 border-slate-800">
            <tr class="text-green-white">
                <th class="text-left p-4">
                    Name
                </th>

                <th class="text-left p-4">
                    Players
                </th>

                <th class="text-left p-4">
                    Created at
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse ($teams as $index => $team)
            <tr>
                <td class="text-left p-4">
                    <span contenteditable="true"
                        wire:keydown.enter='updateTeam({{ $team->id }}, $event.target.textContent)'>{{
                        $team->name
                        }}</span>
                </td>

                <td class="text-left p-4">
                    {{ $team->users_count }}
                </td>

                <td class="text-left p-4">
                    {{ $team->created_at }}
                </td>
            </tr>
            @empty
            <tr>
                <td class="p-4">No teams found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $teams->links('pagination::tailwind') }}
</div>