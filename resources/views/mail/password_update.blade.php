@extends('mail.layout')

@section('content')
    <h1 style="line-height: normal">
        Bonjour {{ $authenticable->last_name }} {{ $authenticable->first_name }}
    </h1>
    <p>
        Votre mot de passe Sartex a été mis à jour aujourd'hui à {{ date('h:m') }}
    </p>
@endsection
