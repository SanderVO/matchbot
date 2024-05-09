@extends('layout.default')

@section('content')

<div class="flex flex-row justify-end items-center mt-8 mb-4">
    <button type="button" class="px-6
        py-2.5
        bg-slate-900
        text-white
        font-medium
        text-xs
        leading-tight
        uppercase
        rounded
        shadow-md
        hover:bg-slate-700 hover:shadow-lg
        focus:bg-slate-700 focus:shadow-lg focus:outline-none focus:ring-0
        active:bg-slate-700 active:shadow-lg
        transition
        duration-150
        ease-in-out" @click="createSeasonModalVisible = true">
        Create season
    </button>
</div>

<livewire:season-table />

<div class="absolute z-10 bg-white max-w-lg mx-auto top-0 left-0 right-0 bottom-0 my-16 p-8 rounded drop-shadow-lg h-full"
    role="dialog" tabindex="-1" :class="{ 'hidden': !createSeasonModalVisible }" x-show="createSeasonModalVisible"
    x-on:click.away="createSeasonModalVisible = false" x-cloak x-on:season-created="createSeasonModalVisible = false"
    x-transition>
    <livewire:create-season />
</div>

<div class="overlay" x-show="createSeasonModalVisible" x-cloak></div>

@endsection