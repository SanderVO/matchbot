<form class="flex flex-col bg-slate-800 border border-slate-600 rounded p-4 w-max m-auto" wire:submit="save">
    @foreach ($teamResults as $index => $teamResult)
    <div wire:key="{{ $teamResult['id'] }}">
        <h2 class="h2 font-bold mb-4">{{ $teamResult['team']['name'] }}</h2>

        <div class="mb-4 w-64">
            <label class="block" for="teamResults.{{ $index }}.score">
                <span>Score</span>

                <input class="form-input block px-2 py-2 rounded-md w-full text-black" type="number" min="0"
                    wire:model="teamResults.{{ $index }}.score" value="{{ $teamResult['score'] }}" />

                @error('teamResults.{{ $index }}.score')
                <div class="bg-red-600 w-100 p-4 mt-4 color-white rounded text-white">
                    {{ $message }}
                </div>
                @enderror
            </label>
        </div>

        <div class="mb-4 w-64">
            <label class="block" for="teamResults.{{ $index }}.crawl_score">
                <span>Crawl score</span>

                <input class="form-input block px-2 py-2 rounded-md w-full text-black" type="number" min="0"
                    wire:model="teamResults.{{ $index }}.crawl_score"
                    value="{{ $teamResult['crawl_score'] }}"></textarea>

                @error('teamResults.{{ $index }}.crawl_score')
                <div class="bg-red-600 w-100 p-4 mt-4 color-white rounded text-white">
                    {{ $message }}
                </div>
                @enderror
            </label>
        </div>

        @foreach ($teamResult['team_result_users'] as $indexUser => $teamResultUser)
        <div class="mb-4 w-64">
            <label class="block" for="teamResultUsers.{{ $indexUser }}.score">
                <span>Score ({{ $teamResultUser['user']['name'] }})</span>

                <input class="form-input block px-2 py-2 rounded-md w-full text-black" type="number" min="0"
                    wire:model="teamResults.{{ $index }}.team_result_users.{{ $indexUser }}.score"
                    value="{{ $teamResultUser['score'] }}" />

                @error('teamResultUsers.{{ $indexUser }}.score')
                <div class="bg-red-600 w-100 p-4 mt-4 color-white rounded text-white">
                    {{ $message }}
                </div>
                @enderror
            </label>
        </div>

        <div class="mb-4 w-64">
            <label class="block" for="teamResultUsers.{{ $indexUser }}.crawl_score">
                <span>Crawl score ({{ $teamResultUser['user']['name'] }})</span>

                <input class="form-input block px-2 py-2 rounded-md w-full text-black" type="number" min="0"
                    wire:model="teamResults.{{ $index }}.team_result_users.{{ $indexUser }}.crawl_score"
                    value="{{ $teamResultUser['score'] }}" />

                @error('teamResultUsers.{{ $indexUser }}.crawl_score')
                <div class="bg-red-600 w-100 p-4 mt-4 color-white rounded text-white">
                    {{ $message }}
                </div>
                @enderror
            </label>
        </div>
        @endforeach
    </div>
    @endforeach

    <div class="mb-4 w-64">
        <label class="block" for="season">
            <span>Comment</span>

            <textarea class="form-textarea block px-2 py-2 rounded-md w-full text-black"
                wire:model="comment"></textarea>

            @error('comment')
            <div class="bg-red-600 w-100 p-4 mt-4 color-white rounded text-white">
                {{ $message }}
            </div>
            @enderror
        </label>
    </div>

    <div class="my-4">
        <button
            class="bg-slate-900 border border-slate-700 transition-colors w-100 py-2 px-4 color-white rounded text-white w-full hover:bg-slate-700"
            type="submit" wire:loading.attr="disabled">
            <span wire:loading.remove>
                Save
            </span>

            <span wire:loading wire:target="save">
                Saving...
            </span>
        </button>
    </div>
</form>