@extends('layout.default')

@section('content')

<h1>{{ $name }}</h1>

<div>
    <ul>
        @forelse ($events as $event)
        <li>{{ $event->name }}</li>
        @empty
        <p>No matches found</p>
        @endforelse
    </ul>
</div>

{{ $events->links() }}

@endsection