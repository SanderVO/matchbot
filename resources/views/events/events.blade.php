@extends('layout.default')

@section('content')

<div>Pagina: {{ $name }}</div>

{{ $events->links() }}

@endsection