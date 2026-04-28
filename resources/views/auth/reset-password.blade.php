@extends('layouts.app')

@section('title', 'Réinitialiser le mot de passe - DK Soft')

@section('nav')
    <a href="{{ url('/') }}" class="nav-link">Accueil</a>
    <div class="header-badge">Espace Vendeur</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/authentification/log.css') }}">
@endpush

@section('content')

<div class="particles" id="particles"></div>

<div class="login-page">

    <div class="login-card">

        <div class="user-icon">🔒</div>

        <div class="login-eyebrow">Espace vendeur</div>
        <h2 class="login-title">
            <span style="color:var(--orange)">Nouveau</span>
            <span style="color:var(--blue)">Mot de passe</span>
        </h2>
        <p class="login-subtitle">Choisissez un nouveau mot de passe sécurisé</p>

        <div class="form-divider">
            <div class="form-divider-line"></div>
            <span class="form-divider-text">Réinitialisation</span>
            <div class="form-divider-line"></div>
        </div>

        @if($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.update') }}" autocomplete="off">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="field-group">
                <label class="field-label">Adresse Email</label>
                <input
                    type="email"
                    name="email"
                    required
                    class="field-input"
                    placeholder="vendeur@dksoft.tn"
                    autocomplete="off"
                    value="{{ old('email', $email) }}"
                >
                @error('email')
                    <span style="color:#c0401a; font-size:12px; margin-top:4px; display:block;">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Nouveau mot de passe</label>
                <input
                    type="password"
                    name="password"
                    required
                    class="field-input"
                    placeholder="Min. 8 caractères"
                    autocomplete="new-password"
                >
                @error('password')
                    <span style="color:#c0401a; font-size:12px; margin-top:4px; display:block;">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            <div class="field-group">
                <label class="field-label">Confirmer le mot de passe</label>
                <input
                    type="password"
                    name="password_confirmation"
                    required
                    class="field-input"
                    placeholder="Répétez le mot de passe"
                    autocomplete="new-password"
                >
            </div>

            <button type="submit" class="btn-submit">Réinitialiser →</button>
        </form>

        <div class="card-footer">
            <a href="{{ route('login') }}"     class="card-link">← Retour à la connexion</a>
            <a href="{{ route('selection') }}" class="card-link">← Accueil</a>
        </div>

    </div>

</div>

@push('scripts')
<script src="{{ asset('js/authentification/reset-password.js') }}"></script>
@endpush

@endsection