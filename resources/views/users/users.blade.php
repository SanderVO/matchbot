@extends('layout.default')

@section('content')

<div>Pagina: {{ $name }}</div>

{{ $users->links() }}

@endsection