<div>
    <table class="table-auto w-full mb-4">
        <thead class="bg-evening">
            <tr>
                <th class="text-left p-4">
                    ID
                </th>

                <th class="text-left p-4">
                    Name
                </th>

                <th class="text-left p-4">
                    Aangemaakt op
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse ($seasons as $season)
            <tr>
                <td class="text-left p-4">
                    {{ $season->id }}
                </td>

                <td class="text-left p-4">
                    {{ $season->name }}
                </td>

                <td class="text-left p-4">
                    {{ $season->created_at }}
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