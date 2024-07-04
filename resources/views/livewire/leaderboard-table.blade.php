<div>
    <div class="mb-4">
        <select class="bg-slate-900 border-slate-800" wire:model="scorableType"
            wire:change='onScorableTypeChange($event.target.value)'>
            <option value="App\Models\Team">Teams</option>
            <option value="App\Models\User">Players</option>
        </select>
    </div>

    <div class="border-slate-800 rounded-md">
        <table class="table-auto w-full mb-4 bg-slate-900 border-slate-800 border-4 rounded-lg">
            <thead class="border-b-4 border-slate-800">
                <tr class="text-green-white">
                    <th class="text-left p-4">
                        Team
                    </th>

                    <th class="text-left p-4">
                        Played
                    </th>

                    <th class="text-left p-4">
                        W/L
                    </th>

                    <th class="text-left p-4">
                        W/L %
                    </th>

                    <th class="text-left p-4">
                        ELO
                    </th>

                    <th class="text-left p-4">
                        +-
                    </th>

                    <th class="text-left p-4">
                        Crawl Score
                    </th>

                    <th class="text-left p-4">
                        Avg. Crawl
                    </th>

                    <th class="text-left p-4">
                        Avg. Score
                    </th>

                    <th class="text-left p-4">
                        Form
                    </th>

                    <th class="text-left p-4">
                        Streak
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse ($userEloRatings as $userEloRating)
                <tr class="text-white-">
                    <td class="text-left p-4">
                        {{ $userEloRating->scorable->name }}
                    </td>

                    <td class="text-left p-4">
                        {{ $userEloRating->played }}
                    </td>

                    <td class="text-left p-4">
                        <span class="text-green-400">W{{ $userEloRating->wins }}</span> - <span class="text-red-400">L{{
                            $userEloRating->losses }}</span>
                    </td>

                    <td class="text-left p-4">
                        <span
                            class="{{ $userEloRating->win_lose_percentage > 49 ? 'text-green-400' : 'text-red-400' }}">{{
                            $userEloRating->win_lose_percentage }}%</span>
                    </td>

                    <td class="text-left p-4 font-bold">
                        {{ $userEloRating->elo_rating }}
                    </td>

                    <td
                        class="text-left p-4 {{ $userEloRating->elo_rating_difference >= 0 ? 'text-green-400' : 'text-red-400' }}">
                        {{ $userEloRating->elo_rating_difference }}
                    </td>

                    <td class="text-left p-4 font-bold">
                        {{ $userEloRating->total_crawl_score }}
                    </td>

                    <td class="text-left p-4 font-bold">
                        {{ $userEloRating->avg_crawl }}
                    </td>

                    <td class="text-left p-4 font-bold">
                        {{ $userEloRating->avg_score }}
                    </td>

                    <td class="text-left p-4 font-bold">
                        @foreach ($userEloRating->streak as $result)
                        <span class="{{ $result === 'W' ? 'text-green-400' : 'text-red-400' }}">{{ $result }}</span>
                        @endforeach
                    </td>

                    <td class="text-left p-4 font-bold">
                        <span
                            class="{{ $userEloRating->current_streak_type === 'W' ? 'text-green-400' : 'text-red-400' }}">{{
                            $userEloRating->current_streak_type }}{{ $userEloRating->current_streak }}</span>
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
</div>