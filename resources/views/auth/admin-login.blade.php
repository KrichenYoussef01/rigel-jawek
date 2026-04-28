@extends('layouts.app')

@section('title', 'Connexion Administration - DK Soft')

@section('nav')
    <div class="header-badge">Zone Administrateur</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/adminlog.css') }}">
@endpush

@section('content')

<div class="particles" id="particles"></div>

<div class="login-page">
    <div style="width:100%;max-width:460px;">

        <div class="login-card">

            <div class="shield-icon">🛡️</div>

            <div class="login-eyebrow">Panneau d'administration</div>
            <h1 class="login-title">Accès <span style="color:var(--blue-light)">Admin</span><span style="color:var(--orange)">istrateur</span></h1>
            <p class="login-subtitle">Veuillez vous identifier pour gérer le système</p>

            <div class="form-divider">
                <div class="form-divider-line"></div>
                <span class="form-divider-text">Identifiez-vous</span>
                <div class="form-divider-line"></div>
            </div>

            <form action="{{ route('admin.login.submit') }}" method="POST">
                @csrf

                @if($errors->any())
                    <div class="alert-error">{{ $errors->first() }}</div>
                @endif

                <div class="field-group">
                    <label class="field-label">Email Professionnel</label>
                    <input type="email" name="email" required
                        class="field-input"
                        placeholder="admin@dksoft.tn"
                        value="{{ old('email') }}">
                </div>

                <div class="field-group">
                    <label class="field-label">Mot de passe</label>
                    <input type="password" name="password" required
                        class="field-input"
                        placeholder="••••••••">
                </div>

                {{-- reCAPTCHA --}}
                <div class="recaptcha-wrap">
                    {!! NoCaptcha::display() !!}
                </div>

                <button type="submit" class="btn-submit">
                    Accéder au Panel →
                </button>
            </form>

            <div class="card-footer">
                <a href="{{ route('selection') }}" class="card-link">← Retour à la sélection du rôle</a>
            </div>

        </div>

        <p class="security-badge">Sécurité DK Soft · Zone Restreinte</p>

    </div>
</div>

@push('scripts')
{!! NoCaptcha::renderJs() !!}
<script>
   
</script>
@endpush
 <script src="{{ asset('js/admin/adminlog.js') }}"></script>
@endsection