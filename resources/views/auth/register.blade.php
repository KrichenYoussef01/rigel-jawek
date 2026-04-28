@extends('layouts.app')

@section('title', 'Inscription - DK Soft')

@section('nav')
    <div class="header-badge">Espace Vendeur</div>
@endsection


<link rel="stylesheet" href="{{ asset('css/authentification/register.css') }}">
@section('content')
<div class="particles" id="particles"></div>

<div class="register-page">
    <div class="register-card">
        <div class="user-icon">👤</div>

        <div class="register-eyebrow">Nouveau Vendeur</div>
        <h2 class="register-title">
            <span style="color:#e85c1a">TikTok</span>
            <span style="color:#1e88e5">Frip</span>
        </h2>
        <p class="register-subtitle">Créez votre compte pour commencer l'aventure</p>

        <div class="form-divider">
            <div class="form-divider-line"></div>
            <span class="form-divider-text">Inscription</span>
            <div class="form-divider-line"></div>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf

            @if($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            <div class="field-group">
                <label class="field-label">Nom complet</label>
                <input type="text" name="name" required class="field-input" placeholder="Votre nom complet" value="{{ old('name') }}">
            </div>

            <div class="field-group">
                <label class="field-label">Adresse Email</label>
                <input type="email" name="email" required class="field-input" placeholder="vendeur@dksoft.tn" value="{{ old('email') }}">
            </div>

            <div class="field-group">
                <label class="field-label">Mot de passe</label>
                <input type="password" name="password" required class="field-input" placeholder="••••••••">
            </div>

            <div class="field-group">
                <label class="field-label">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" required class="field-input" placeholder="••••••••">
            </div>

            <button type="submit" class="btn-submit">S'inscrire maintenant →</button>
        </form>

        <div class="card-footer">
            <a href="{{ route('login') }}" class="card-link">Déjà un compte ?</a>
            <a href="{{ route('selection') }}" class="card-link">← Retour</a>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/authentification/register.js') }}"></script>
@endpush
@endsection