@extends('layout.default')

@section('content')

<div class="flex flex-row justify-end items-center mt-8 mb-4">
    <a href="/events/create" class="px-6
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
            ease-in-out">
        Create event
    </a>
</div>

<livewire:event-table />

@endsection