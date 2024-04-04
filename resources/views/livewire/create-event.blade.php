<form class="flex flex-col bg-slate-600 rounded p-4" wire:submit="save">
    <div class="mb-4 w-64">
        <label class="block" for="season">
            <span>Season</span>

            <select class="form-select block px-2 py-2 rounded-md w-full text-black" wire:model.live="seasonId">
                <option checked="true">Choose a season</option>

                @foreach ($seasons as $season)
                <option value="{{ $season->id }}">
                    {{ $season->name }}
                </option>
                @endforeach
            </select>

            @error('seasonId')
            <div class="bg-red-600 w-100 p-4 mt-4 color-white rounded text-white">
                {{ $message }}
            </div>
            @enderror
        </label>
    </div>

    <div class="mb-4 w-64">
        <label class="block" for="sport">
            <span>Sport</span>

            <select class="form-select block px-2 py-2 rounded-md w-full text-black" name="sport"
                wire:model.live="sportId" wire:change='getEventTypes'>
                <option checked="true">Choose a sport</option>

                @foreach ($sports as $sport)
                <option value="{{ $sport->id }}">
                    {{ $sport->name }}
                </option>
                @endforeach
            </select>

            @error('sportId')
            <div class="bg-red-600 w-100 p-4 mt-4 color-white rounded text-white">
                {{ $message }}
            </div>
            @enderror
        </label>
    </div>

    @if (isset($types))
    <div class="mb-4 w-64">
        <label class="block" for="type">
            <span>Type</span>

            <select class="form-select block px-2 py-2 rounded-md w-full text-black"" name=" type"
                wire:model.live="eventTypeId">
                <option checked="true">Choose a type</option>

                @foreach ($types as $type)
                <option value="{{ $type->id }}">
                    {{ $type->name }}
                </option>
                @endforeach
            </select>

            @error('typeId')
            <div class="bg-red-600 w-100 p-4 mt-4 color-white rounded text-white">
                {{ $message }}
            </div>
            @enderror
        </label>
    </div>
    @endif

    <div class="mb-4 w-64">
        <livewire:search-users-typeahead organizationId="1" />

        @error('userIds')
        <div class="bg-red-600 w-100 p-4 mt-4 color-white rounded text-white">
            {{ $message }}
        </div>
        @enderror
    </div>

    <div class="my-4">
        <button
            class="bg-slate-800 transition-colors w-100 py-2 px-4 color-white rounded text-white w-full hover:bg-slate-700"
            type="submit" wire:loading.attr="disabled">
            <span wire:loading.remove>
                Generate
            </span>

            <span wire:loading>
                Generating...
            </span>
        </button>

        @if ($saveIsSuccessful)
        <div class="bg-green-600 w-100 p-4 mt-4 color-white rounded text-white">
            Event created
        </div>
        @endif
    </div>
</form>