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
                    W/L
                </th>

                <th class="text-left p-4">
                    W/L %
                </th>

                <th class="text-left p-4">
                    ELO Rating
                </th>

                <th class="text-left p-4">
                    +-
                </th>
            </tr>
        </thead>

        <tbody>
            @forelse ($userEloRatings as $userEloRating)
            <tr class="text-white-">
                <td class="text-left p-4 ">
                    {{ $userEloRating->id }}
                </td>

                <td class="text-left p-4">
                    {{ $userEloRating->scorable->name }}
                </td>

                <td class="text-left p-4">
                    <span class="text-green-400">W{{ $userEloRating->wins }}</span> - <span class="text-red-400">L{{
                        $userEloRating->losses }}</span>
                </td>

                <td class="text-left p-4">
                    <span class="{{ $userEloRating->win_lose_percentage > 49 ? 'text-green-400' : 'text-red-400' }}">{{
                        $userEloRating->win_lose_percentage }}%</span>
                </td>

                <td class="text-left p-4 font-bold">
                    {{ $userEloRating->elo_rating }}
                </td>

                <td
                    class="text-left p-4 {{ $userEloRating->elo_rating_difference >= 0 ? 'text-green-400' : 'text-red-400' }}">
                    {{ $userEloRating->elo_rating_difference }}
                </td>
            </tr>
            @empty
            <tr>
                <td class="p-4">No ratings found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $userEloRatings->links('pagination::tailwind') }}
</div>