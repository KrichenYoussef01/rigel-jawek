@extends('layouts.app')

@section('title', 'Mot de passe oublié - DK Soft')

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

        <div class="user-icon">🔑</div>

        <div class="login-eyebrow">Espace vendeur</div>
        <h2 class="login-title">
            <span style="color:var(--orange)">Mot de passe</span>
            <span style="color:var(--blue)">Oublié</span>
        </h2>
        <p class="login-subtitle">Entrez votre email pour recevoir un lien de réinitialisation</p>

        <div class="form-divider">
            <div class="form-divider-line"></div>
            <span class="form-divider-text">Récupération</span>
            <div class="form-divider-line"></div>
        </div>

        {{-- ✅ Message succès --}}
        @if(session('success'))
            <div style="
                background: rgba(34,197,94,0.08);
                border: 1px solid rgba(34,197,94,0.3);
                color: #15803d;
                border-radius: 12px;
                padding: 12px 16px;
                font-size: 13px;
                text-align: center;
                margin-bottom: 20px;
            ">
                {{ session('success') }}
            </div>
        @endif

        {{-- ✅ Erreur --}}
        @if($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" autocomplete="off">
            @csrf

            <div class="field-group">
                <label class="field-label">Adresse Email</label>
                <input
                    type="email"
                    name="email"
                    required
                    class="field-input"
                    placeholder="vendeur@dksoft.tn"
                    autocomplete="off"
                    value="{{ old('email') }}"
                >
            </div>

            <button type="submit" class="btn-submit">Envoyer le lien →</button>
        </form>

        <div class="card-footer">
            <a href="{{ route('login') }}"    class="card-link">← Retour à la connexion</a>
            <a href="{{ route('selection') }}" class="card-link">← Accueil</a>
        </div>

    </div>

</div>

@push('scripts')
<script>
    const container = document.getElementById('particles');
    const colors = ['rgba(26,123,191,0.3)','rgba(47,168,232,0.2)','rgba(232,84,26,0.25)','rgba(255,122,61,0.18)'];
    for (let i = 0; i < 25; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        const size = Math.random() * 4 + 1;
        p.style.cssText = `
            left: ${Math.random() * 100}%;
            width: ${size}px; height: ${size}px;
            background: ${colors[Math.floor(Math.random() * colors.length)]};
            animation-duration: ${Math.random() * 15 + 10}s;
            animation-delay: ${Math.random() * 10}s;
        `;
        container.appendChild(p);
    }
</script>
@endpush

@endsection