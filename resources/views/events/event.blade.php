@extends('layout.default')

@section('content')

<div class="flex flex-row justify-center items-center mt-8 mb-4">
    <h1 class="h1">
        @foreach ($event->teamResults as $teamResult)
        {{ $teamResult->team->name }}
        @endforeach
    </h1>
</div>

@endsection