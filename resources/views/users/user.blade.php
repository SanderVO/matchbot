@extends('layout.default')

@section('content')

<h1>{{ $user->username }}</h1>

<div>Gebruiker: {{ $user->username }}</div>

@endsection