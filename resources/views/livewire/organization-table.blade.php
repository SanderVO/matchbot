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
                    Deelnemers
                </th>

                <th class="text-left border-b border-slate-100 p-4">
                    Aangemaakt op
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse ($organizations as $organization)
            <tr>
                <td class="text-left border-b border-slate-100 p-4">
                    {{ $organization->id }}
                </td>

                <td class="text-left border-b border-slate-100 p-4">
                    {{ $organization->name }}
                </td>

                <td class="text-left border-b border-slate-100 p-4">
                    {{ $organization->users_count }}
                </td>

                <td class="text-left border-b border-slate-100 p-4">
                    {{ $organization->created_at }}
                </td>
            </tr>
            @empty
            <tr>
                <td class="border-b border-slate-100 p-4">No organizations found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $organizations->links('pagination::tailwind') }}
</div>