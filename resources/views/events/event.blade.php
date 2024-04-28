@extends('layout.default')

@section('content')

<div class="flex flex-col justify-center items-center mt-8 mb-4">
    @if (session('status'))
    <div class="bg-green-600 w-100 p-4 mt-4 color-white rounded text-white mb-4">
        {{ session('status') }}
    </div>
    @endif

    <h1 class="h1 mb-8">
        @foreach ($event->teamResults as $teamResult)
        {{ $teamResult->team->name }}
        @endforeach
    </h1>

    <div class="flex flex-col sm:flex-row mb-8 m-auto">
        @foreach ($event->teamResults as $teamResult)
        <div class="flex flex-col bg-slate-800 border border-slate-600 rounded p-4 w-full first:mr-4">
            <h2 class="large mb-4">{{ $teamResult->team->name }}</h2>

            <div class="flex flex-col mb-2">
                <span class="font-bold">Score</span>
                <span>{{ $teamResult->score }}</span>
            </div>

            <div class="flex flex-col mb-2">
                <span class="font-bold">Crawl score</span>
                <span>{{ $teamResult->crawl_score }}</span>
            </div>

            @foreach ($teamResult->teamResultUsers as $teamResultUser)
            <div class="mb-2">
                <div class="flex flex-col">
                    <span class="font-bold">Score {{ $teamResultUser->user->name }}</span>
                    <span>{{ $teamResultUser->score }}</span>
                </div>

                <div class="flex flex-col">
                    <span class="font-bold">Crawl score {{ $teamResultUser->user->name }}</span>
                    <span>{{ $teamResultUser->crawl_score }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @endforeach
    </div>

    <livewire:update-event :eventId="$event->id" />
</div>

@endsection