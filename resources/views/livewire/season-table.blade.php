<div>
    <table class="table-auto w-full border mb-4">
        <thead>
            <tr>
                <th class="text-left border-b border-slate-100 p-4">
                    ID
                </th>

                <th class="text-left border-b border-slate-100 p-4">
                    Name
                </th>

                <th class="text-left border-b border-slate-100 p-4">
                    Aangemaakt op
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse ($seasons as $season)
            <tr>
                <td class="text-left border-b border-slate-100 p-4">
                    {{ $season->id }}
                </td>

                <td class="text-left border-b border-slate-100 p-4">
                    {{ $season->name }}
                </td>

                <td class="text-left border-b border-slate-100 p-4">
                    {{ $season->created_at }}
                </td>
            </tr>
            @empty
            <tr>
                <td class="border-b border-slate-100 p-4">No seasons found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $seasons->links('pagination::tailwind') }}
</div>