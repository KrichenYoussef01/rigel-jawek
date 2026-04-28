@extends('layouts.app')
@section('title', 'DKSoft — Gestion des Commandes Live')
@section('content-class', 'block !p-0')

@section('nav')
    <a href="{{ url('/') }}"  class="nav-link">Accueil</a>
    <a href="#about"          class="nav-link">À propos</a>
    <a href="#features"       class="nav-link">Fonctionnalités</a>
    <a href="#how"            class="nav-link">Comment ça marche</a>
    <a href="#pricing"        class="nav-link">Tarifs</a>

    @auth
        @php
            $hasActivePlan = \App\Models\Payment::where('user_id', auth()->id())
                ->where('status', 'accepte')
                ->where('expires_at', '>', now())
                ->exists();
        @endphp

        @if($hasActivePlan)
            <a href="{{ route('dashboard') }}" class="btn-header btn-blue">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        @else
            <a href="{{ route('pricing') }}" class="btn-header btn-orange">
                <i class="fas fa-star"></i> Voir les plans
            </a>
        @endif
    @else
        <a href="{{ route('login') }}" class="btn-header btn-orange">
            <i class="fas fa-sign-in-alt"></i> Se connecter vendeur
        </a>
    @endauth
@endsection
@push('styles')
<link rel="stylesheet" href="{{ asset('css/acceuil/acceuil.css') }}">
@endpush

@section('content')
<div style="font-family:'Montserrat',sans-serif;">


<div class="lp-hero">
    <h1>Gérez vos commandes<br/><em>live en temps réel.</em></h1>
    <p>Extrayez automatiquement les commandes de vos lives Facebook & TikTok. Simple, rapide et fiable.</p>
    
    <div class="hero-video">
        <iframe src="https://www.youtube.com/embed/KyvGWYLgkeY?rel=0&modestbranding=1"
            title="Démonstration DKSoft" frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen>
        </iframe>
    </div>
</div>

{{-- ABOUT --}}
<div class="lp-about" id="about">
  <div class="container">
    <div class="about-grid">
      <div>
        <div class="eyebrow">À propos de RigelJawek </div>
        <h2>C'est quoi RigelJawek ?</h2>
        <p>RigelJawek est une plateforme de gestion intelligente des commandes passées lors de vos sessions live sur Facebook et TikTok.</p>
        <p>Plus besoin de noter manuellement : notre système extrait, organise et archive toutes les commandes en temps réel.</p>
        <div class="tags">
  <span class="tag"><i class="fas fa-video"></i> Extraction Live</span>
  <span class="tag"><i class="fas fa-terminal"></i> Commandes</span>
  <span class="tag"><i class="fas fa-bolt"></i> Temps réel</span>
  <span class="tag"><i class="fas fa-chart-line"></i> Historique</span>
</div>
      </div>
      <div class="stats-grid">
        
        <div class="stat-box"><div class="v"><em>2</em></div><div class="l">Plateformes</div></div>
        <div class="stat-box"><div class="v"><em>24</em>/7</div><div class="l">Disponibilité</div></div>
      </div>
    </div>
  </div>
</div>

{{-- FEATURES --}}
<div class="lp-features" id="features">
  <div class="sec-head">
    <div class="eyebrow">Fonctionnalités</div>
    <h2>Fonctionnalités remarquables<br/>sur lesquelles vous pouvez compter !</h2>
    <p>Des outils puissants pour simplifier vos ventes en live et booster votre productivité.</p>
  </div>
  <div class="feat-grid">
    <div class="feat-card">
      <div class="ico"><i class="fab fa-facebook"></i></div>
      <h3>Facebook Live</h3>
      <p>Extraction automatique des commandes depuis vos sessions Facebook Live en temps réel.</p>
    </div>
    <div class="feat-card">
      <div class="ico"><i class="fab fa-tiktok"></i></div>
      <h3>TikTok Live</h3>
      <p>Capturez chaque commande passée lors de vos lives TikTok sans intervention manuelle.</p>
    </div>
    <div class="feat-card">
      <div class="ico"><i class="fas fa-clipboard-list"></i></div>
      <h3>Gestion des commandes</h3>
      <p>Tableau de bord centralisé pour visualiser, filtrer et traiter toutes vos commandes.</p>
    </div>
  </div>
</div>

{{-- HOW --}}
<!-- À placer dans <head> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="lp-how" id="how">
  <div class="sec-head">
    <div class="eyebrow">Processus</div>
    <h2>Comment ça fonctionne ?</h2>
  </div>
  <div class="how-grid">
    <div class="how-step">
      <div class="step-num"><i class="fas fa-key"></i></div>
      <h4>Connectez-vous</h4>
      <p>Accédez à votre espace client avec vos identifiants.</p>
    </div>
    <div class="how-step">
      <div class="step-num"><i class="fas fa-satellite-dish"></i></div>
      <h4>Lancez votre live</h4>
      <p>Démarrez votre session Facebook ou TikTok Live.</p>
    </div>
    <div class="how-step">
      <div class="step-num"><i class="fas fa-bolt"></i></div>
      <h4>Extraction auto</h4>
      <p>DKSoft capture toutes les commandes en temps réel.</p>
    </div>
    <div class="how-step">
      <div class="step-num"><i class="fas fa-box"></i></div>
      <h4>Gérez & livrez</h4>
      <p>Traitez et expédiez depuis votre tableau de bord.</p>
    </div>
  </div>
</div>
{{-- PRICING --}}
<div class="lp-pricing" id="pricing">
  <div class="sec-head">
    <div class="eyebrow">Tarifs</div>
    <h2>Des formules adaptées à votre activité</h2>
    <p>Choisissez le plan qui correspond à vos besoins et commencez à gérer vos commandes live dès aujourd'hui.</p>
  </div>

  <div class="pricing-grid">

    {{-- STARTER --}}
    <div class="price-card">
      <div class="price-icon">🌱</div>
      <div class="price-name">Starter</div>
      <div class="price-amount"><span>DT</span>29</div>
      <div class="price-period">par mois · sans engagement</div>
      <div class="price-divider"></div>
      <ul class="price-features">
        <li><span class="chk"><i class="fas fa-check"></i></span> 1 compte vendeur</li>
        <li><span class="chk"><i class="fas fa-check"></i></span> Facebook Live uniquement</li>
        <li><span class="chk"><i class="fas fa-check"></i></span> Jusqu'à 200 commandes/mois</li>
        <li><span class="chk"><i class="fas fa-check"></i></span> Tableau de bord basique</li>
        <li class="off"><span class="chk"><i class="fas fa-times"></i></span> TikTok Live</li>
        <li class="off"><span class="chk"><i class="fas fa-times"></i></span> Export Excel / CSV</li>
        <li class="off"><span class="chk"><i class="fas fa-times"></i></span> Support prioritaire</li>
      </ul>
      <a href="{{ route('login') }}" class="btn-plan btn-plan-outline">Commencer</a>
    </div>

    {{-- PRO (populaire) --}}
    <div class="price-card popular">
      <div class="popular-badge">⭐ Le plus populaire</div>
      <div class="price-icon">🚀</div>
      <div class="price-name">Pro</div>
      <div class="price-amount"><span>DT</span>69</div>
      <div class="price-period">par mois · sans engagement</div>
      <div class="price-divider"></div>
      <ul class="price-features">
        <li><span class="chk"><i class="fas fa-check"></i></span> 3 comptes vendeurs</li>
        <li><span class="chk"><i class="fas fa-check"></i></span> Facebook & TikTok Live</li>
        <li><span class="chk"><i class="fas fa-check"></i></span> Commandes illimitées</li>
        <li><span class="chk"><i class="fas fa-check"></i></span> Tableau de bord avancé</li>
        <li><span class="chk"><i class="fas fa-check"></i></span> Export Excel / CSV</li>
        <li><span class="chk"><i class="fas fa-check"></i></span> Historique 6 mois</li>
        <li class="off"><span class="chk"><i class="fas fa-times"></i></span> Support prioritaire 24/7</li>
      </ul>
      <a href="{{ route('login') }}" class="btn-plan btn-plan-fill">Choisir Pro</a>
    </div>

    {{-- BUSINESS --}}
    <div class="price-card">
      <div class="price-icon">🏢</div>
      <div class="price-name">Business</div>
      <div class="price-amount"><span>DT</span>149</div>
      <div class="price-period">par mois · sans engagement</div>
      <div class="price-divider"></div>
      <ul class="price-features">
        <li><span class="chk"><i class="fas fa-check"></i></span> Comptes vendeurs illimités</li>
        <li><span class="chk"><i class="fas fa-check"></i></span> Facebook & TikTok Live</li>
        <li><span class="chk"><i class="fas fa-check"></i></span> Commandes illimitées</li>
        <li><span class="chk"><i class="fas fa-check"></i></span> Tableau de bord complet</li>
        <li><span class="chk"><i class="fas fa-check"></i></span> Export Excel / CSV</li>
        <li><span class="chk"><i class="fas fa-check"></i></span> Historique complet</li>
        <li><span class="chk"><i class="fas fa-check"></i></span> Support prioritaire 24/7</li>
      </ul>
      <a href="{{ route('login') }}" class="btn-plan btn-plan-outline">Contacter l'équipe</a>
    </div>

  </div>
</div>
{{-- CTA --}}
<div class="lp-cta">
  <h2>Prêt à simplifier votre activité ?</h2>
  <p>Rejoignez les vendeurs qui font confiance à DKSoft pour gérer leurs commandes live.</p>
  <a href="/login" class="btn-main">🚀 Accéder à mon espace</a>
</div>

{{-- NEWSLETTER --}}
<div class="lp-nl">
  <h3>Abonnez-vous à notre newsletter</h3>
  <p>Recevez chaque semaine les dernières actualités et conseils exclusifs</p>
  <div class="nl-row">
    <input type="email" placeholder="Votre adresse e-mail">
    <button>S'abonner</button>
  </div>
</div>

{{-- FOOTER --}}
<div class="lp-footer">
  <div class="footer-grid">
    <div class="f-brand">
      <div class="f-logo"><span class="b">DK</span><span class="o">Soft</span></div>
      <p>DKSoft relie vendeurs et plateformes sociales pour gérer les commandes live en temps réel et booster les ventes.</p>
    </div>
    <div class="f-col">
      <h5>Support</h5>
      <ul>
        <li><a href="#">Politique de confidentialité</a></li>
        <li><a href="#">Conditions générales</a></li>
      </ul>
    </div>
    <div class="f-col">
      <h5>Contactez-nous</h5>
      <ul>
        <li class="ci">📍 Tunisie</li>
        <li class="ci">📧 support@dksoft.tn</li>
        <li class="ci">📞 +216 XX XXX XXX</li>
      </ul>
    </div>
  </div>
  <div class="f-bottom">© 2026. Powered by <strong style="color:rgba(255,255,255,.4)">DKSoft</strong></div>
</div>

</div>
@endsection