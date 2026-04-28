@extends('layouts.app')

@section('title', 'Connexion - DK Soft')

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

        <div class="user-icon">🧑‍💼</div>

        <div class="login-eyebrow">Espace vendeur</div>
        <h2 class="login-title">
            <span style="color:var(--orange)">TikTok</span>
            <span style="color:var(--blue)">Frip</span>
        </h2>
        <p class="login-subtitle">Identifiez-vous pour accéder à votre espace</p>

        <div class="form-divider">
            <div class="form-divider-line"></div>
            <span class="form-divider-text">Connexion</span>
            <div class="form-divider-line"></div>
        </div>

        <form action="{{ route('login') }}" method="POST" autocomplete="off">
    @csrf
    <input type="text" style="display:none" aria-hidden="true">
    <input type="password" style="display:none" aria-hidden="true">

    @if($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="field-group">
        <label class="field-label">Adresse Email</label>
        <input type="email" name="email" required
            class="field-input"
            placeholder="vendeur@dksoft.tn"
            autocomplete="off"
            value="{{ old('email') }}">
    </div>

    <div class="field-group">
        <label class="field-label">Mot de passe</label>
        <input type="password" name="password" required
            class="field-input"
            placeholder="••••••••"
            autocomplete="new-password">
    </div>
    <div class="field-group">
    <label class="field-label">Mot de passe</label>
    <input type="password" name="password" required
        class="field-input"
        placeholder="••••••••"
        autocomplete="new-password">
    
    <a href="{{ route('password.request') }}" 
       style="font-size:12px; color:#1a7bbf; text-align:right; display:block; margin-top:6px;">
        Mot de passe oublié ?
    </a>
</div>

  
    <div class="field-group" style="display:flex;justify-content:center;">
        <div class="g-recaptcha" data-sitekey="{{ config('captcha.sitekey') }}"></div>
    </div>

    @error('g-recaptcha-response')
        <div style="color:#c0401a;font-size:12px;text-align:center;margin-bottom:10px;">
            {{ $message }}
        </div>
    @enderror

    <button type="submit" class="btn-submit">Se connecter →</button>
</form>

<div class="card-footer">
    <a href="{{ route('register') }}" class="card-link">Pas encore de compte ?</a>
    <a href="{{ route('selection') }}" class="card-link">← Retour</a>
</div>

    </div>

</div>

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="{{ asset('js/authentification/login.js') }}"></script>
@endpush

@endsection