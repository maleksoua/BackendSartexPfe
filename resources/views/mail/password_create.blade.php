@extends('mail.layout')

@section('content')
    <h1 style="line-height: normal">
        Bonjour {{ $authenticable->last_name }} {{ $authenticable->first_name }}
    </h1>
    <p>
        Nous avons le plaisir de vous adresser vos identifiants pour vous connecter à l'Espace Pro :
    </p>
    <p>
        Identifiant : {{ $authenticable->email }}
    </p>
    <p>
        Mot de passe: {{ $password }}
    </p>
@endsection
