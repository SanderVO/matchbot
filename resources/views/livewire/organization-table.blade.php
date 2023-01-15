<div>
    <table>
        <thead>
            <tr>
                <th>
                    Name
                </th>
            </tr>
        </thead>
        <tbody>
            @forelse ($organizations as $organization)
            <tr>
                <td>
                    {{ $organization->name }}
                </td>
            </tr>
            @empty
            <tr>
                <td>No organizations found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $organizations->links() }}
</div>