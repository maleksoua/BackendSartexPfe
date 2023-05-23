@extends('mail.layout')

@section('content')
    <h1 style="line-height: normal">
        Bonjour {{ $notifiable->last_name }} {{ $notifiable->first_name }}
    </h1>
    <p>
        Vous avez reçu cet email suite à une demande de modification de mot de passe de votre compte
    </p>
    <p>
        <a href="{{ config('constants.front_password_reset_url') . $token }}" target="_blank">
            <button class="edit_password_button">
                Modifier le mot de passe
            </button>
        </a>
    </p>
    <p>
        Si vous rencontrez des problèmes à cliquer sur le bouton "Modifier le mot de passe", copiez et collez l'url suivant dans votre navigateur : {{ config('constants.front_password_reset_url') . $token }}
    </p>
    <p>
        Si vous n'avez pas effectué cette demande, vous pouvez ignorer cet email.
    </p>
@endsection
