<form class="flex flex-col" wire:submit="save">
    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
            Name
        </label>

        <input wire:model.live="event.name"
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            id="name" type="text" placeholder="Name">

        @error('event.name')
        <div class="bg-red-600 w-100 p-4 mt-4 color-white rounded text-white">
            {{ $message }}
        </div>
        @enderror
    </div>

    <div class="mb-4">
        <button class="bg-blue-600 w-100 py-2 px-4 color-white rounded text-white w-full" type="submit">
            Save
        </button>

        @if ($saveIsSuccessful)
        <div class="bg-green-600 w-100 p-4 mt-4 color-white rounded text-white">
            Event created
        </div>
        @endif
    </div>
</form>