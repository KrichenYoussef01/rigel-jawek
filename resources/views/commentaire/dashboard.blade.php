@extends('layouts.app')

@section('title', 'DKSoft - TikTok Live')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/commentaire/dashboard.css') }}">
@endpush

@section('nav')
    <div x-data x-cloak>
        <button
            x-show="!$store.live?.isLive"
            @click="let el = document.querySelector('.live-overlay');
                    if(el && window.Alpine) Alpine.$data(el).view = 'launch';"
            class="btn-header btn-blue">
            <i class="fas fa-satellite-dish" style="margin-right:4px;"></i>
            Lancer Live
        </button>
        <button
            x-show="$store.live?.isLive"
            @click="window.dispatchEvent(new CustomEvent('stop-live'))"
            class="btn-header btn-danger">
            <i class="fas fa-stop-circle" style="margin-right:4px;"></i>
            Arrêter Live
        </button>
    </div>
@endsection

@section('content')
<div class="live-overlay" x-data="tiktokLive()" x-init="init()">

    {{-- Sidebar --}}
    <aside class="live-sidebar">
        <div class="sidebar-brand">
            <div class="brand-logo">DK</div>
            <div class="brand-text">DKSoft</div>
        </div>

        <nav class="nav-list">
            <div class="nav-item" :class="{ 'active': view === 'codestats' }" @click="view = 'codestats'">
                <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span>
                <span>Dashboard</span>
            </div>
            <div class="nav-item" :class="{ 'active': view === 'dashboard' }" @click="view = 'dashboard'">
                <span class="nav-icon"><i class="fas fa-tv"></i></span>
                <span>Screen</span>
                <span x-show="isLive" class="live-indicator"></span>
            </div>
            <div class="nav-item" :class="{ 'active': view === 'codes' }" @click="view = 'codes'">
                <span class="nav-icon"><i class="fas fa-boxes"></i></span>
                <span>Codes Articles</span>
            </div>
            <div class="nav-item" :class="{ 'active': view === 'historique' }" @click="view = 'historique'">
                <span class="nav-icon"><i class="fas fa-history"></i></span>
                <span>Historique</span>
            </div>
            <div class="nav-item" :class="{ 'active': view === 'sentiments' }" @click="view = 'sentiments'">
                <span class="nav-icon"><i class="fas fa-brain"></i></span>
                <span>Sentiments IA</span>
                <span x-show="aiAnalysisDone"
                    style="position:absolute;right:12px;width:8px;height:8px;
                           background:#a855f7;border-radius:50%;"></span>
            </div>
            @php
    $isSubscribed = auth()->check() && \App\Models\Payment::where('user_id', auth()->id())
        ->where('status', 'accepte')
        ->where('expires_at', '>', now())
        ->exists();
@endphp
            <div class="nav-item" :class="{ 'active': view === 'parametres' }" @click="view = 'parametres'">
                <span class="nav-icon"><i class="fas fa-sliders-h"></i></span>
                <span>Paramétrages</span>
                <span class="subscription-dot {{ $isSubscribed ? 'dot-active' : 'dot-inactive' }}"
                    title="{{ $isSubscribed ? 'Abonnement actif' : 'Aucun abonnement actif' }}">
                </span>
            </div>

        </nav>
    </aside>

    {{-- Main --}}
    <main class="live-main">

        <header class="live-header">
            <h1 class="header-title" x-text="pageTitle"></h1>
            <div class="status-badge" :class="statusClass" x-show="showStatus">
                <span style="width:8px;height:8px;border-radius:50%;display:inline-block;margin-right:6px;"
                      :style="isLive ? 'background:#dc2626' : 'background:#d97706'"></span>
                <span x-text="statusLabel"></span>
            </div>
        </header>

        <div class="live-content">

            {{-- ══════════════════════════════════════════════ --}}
            {{--               VIEW: Launch                     --}}
            {{-- ══════════════════════════════════════════════ --}}
            <div class="section-panel" :class="{ 'active': view === 'launch' }">

                <div x-show="error" class="alert" x-transition>
                    <i class="fas fa-exclamation-triangle"></i>
                    <span x-text="error"></span>
                    <button @click="error=''" style="margin-left:auto;background:none;border:none;cursor:pointer;">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="section-panel" :class="{ 'active': view === 'launch' }">
                    <div class="card-title">
                        <i class="fas fa-play-circle mr-2" style="color:#6366f1;"></i> Démarrer un Live
                    </div>
                    <div class="card-subtitle">Choisissez votre plateforme et lancez l'extraction en direct.</div>

                    <div class="platform-grid">
                        <div class="platform-option" :class="{ 'active': platform === 'tiktok' }" @click="!isBusy && (platform = 'tiktok')">
                            <div class="platform-icon-wrap tiktok">
                                <i class="fab fa-tiktok"></i>
                            </div>
                            <div class="platform-info">
                                <div class="platform-name">TikTok Live</div>
                                <div class="platform-desc">Extraction en direct</div>
                            </div>
                        </div>
                        <div class="platform-option" :class="{ 'active': platform === 'facebook' }" @click="!isBusy && (platform = 'facebook')">
                            <div class="platform-icon-wrap facebook">
                                <i class="fab fa-facebook"></i>
                            </div>
                            <div class="platform-info">
                                <div class="platform-name">Facebook Live</div>
                                <div class="platform-desc">Extraction en direct</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-link mr-1"></i> Lien du live
                        </label>
                        <input type="url" x-model="url" class="form-input"
                               placeholder="https://www.tiktok.com/@username/live"
                               :disabled="isBusy">
                    </div>

                    <button class="btn btn-primary" @click="start()" :disabled="isBusy || !platform || !url">
                        <span x-show="!connecting">
                            <i class="fas fa-rocket mr-1"></i> Démarrer l'extraction
                        </span>
                        <span x-show="connecting">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Connexion...
                        </span>
                    </button>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════ --}}
            {{--               VIEW: Dashboard                  --}}
            {{-- ══════════════════════════════════════════════ --}}
            <div class="section-panel" :class="{ 'active': view === 'dashboard' }">
                <div class="dashboard-wrap">

                    {{-- Left Panel --}}
                    <div style="overflow-y:auto;">

                        {{-- Bouton Enregistrer --}}
                        <div class="button-group">
    <!-- Bouton Enregistrer le Live -->
    <button x-show="aiParsedComments.length > 0 || aiCardsVisible"
            @click="stop()"
            class="btn btn-danger">
        <i class="fas fa-stop-circle mr-1"></i> Enregistrer le Live
    </button>

    <!-- Bouton IA (rose) -->
    <button @click="triggerAIAnalysis($event)"
        x-show="aiParsedComments.length > 0 || aiCardsVisible"
        :disabled="connecting || aiAnalyzing"   {{-- ✅ --}}
        class="btn btn-ai"
        :class="{ 'is-active': aiCardsVisible }">
    <div class="btn__content">
        <span x-show="!aiCardsVisible && !aiAnalyzing" class="btn__icon">
            <i class="fas fa-brain"></i>
        </span>
        <span x-show="aiAnalyzing" class="btn__icon">       {{-- ✅ loading --}}
            <i class="fas fa-spinner fa-spin"></i>
        </span>
        <span x-show="aiCardsVisible && !aiAnalyzing" class="btn__icon">
            <i class="fas fa-stop"></i>
        </span>
        <div class="btn__text">
            <div class="btn__label">
                <span x-show="aiAnalyzing">ANALYSE EN COURS...</span>   {{-- ✅ --}}
                <span x-show="!aiCardsVisible && !aiAnalyzing">LANCER L'ANALYSE IA</span>
                <span x-show="aiCardsVisible && !aiAnalyzing">ARRÊTER L'ANALYSE IA</span>
            </div>
            <div class="btn__sub" x-show="!aiCardsVisible && !aiAnalyzing">
                <span>Powered by Groq · Llama</span>
            </div>
        </div>
        <div x-show="!aiCardsVisible && !aiAnalyzing" class="btn__badge" x-text="aiParsedComments.length + ' msg'"></div>
    </div>
</button>
</div>

                        {{-- Stats --}}
                        <div class="stats-list">
                            <div class="stat-item">
                                <div class="stat-icon blue"><i class="fas fa-comments"></i></div>
                                <div>
                                    <div class="stat-number" x-text="stats.comments">0</div>
                                    <div class="stat-name">Commentaires</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon purple"><i class="fas fa-users"></i></div>
                                <div>
                                    <div class="stat-number" x-text="stats.clients">0</div>
                                    <div class="stat-name">Clients</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon emerald"><i class="fas fa-box"></i></div>
                                <div>
                                    <div class="stat-number" x-text="stats.articles">0</div>
                                    <div class="stat-name">Articles</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon cyan"><i class="fas fa-phone-alt"></i></div>
                                <div>
                                    <div class="stat-number" x-text="stats.phones">0</div>
                                    <div class="stat-name">Numéros</div>
                                </div>
                            </div>
                        </div>

                        {{-- Filtres --}}
                        <div class="filter-card">
                            <div class="filter-header">
                                <i class="fas fa-sliders-h" style="font-size:18px;color:#6366f1;"></i>
                                <h3 style="font-weight:600;color:#1e293b;margin:0;font-size:14px;">Filtres d'affichage</h3>
                            </div>

                            <div @click="toggleFilter('all')" class="filter-option" :class="{ 'active': filterMode === 'all' }">
                                <div class="filter-left">
                                    <div class="filter-icon" style="background:#f3e8ff;color:#9333ea;">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div>
                                        <div class="filter-text">Tous les commentaires</div>
                                        <div class="filter-desc">Flux complet sans filtre</div>
                                    </div>
                                </div>
                                <div class="toggle" :class="{ 'active': filterMode === 'all' }"><div class="toggle-slider"></div></div>
                            </div>

                            <div @click="toggleFilter('code')" class="filter-option" :class="{ 'active': filterMode === 'code' }">
                                <div class="filter-left">
                                    <div class="filter-icon" style="background:#dcfce7;color:#166534;">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div>
                                        <div class="filter-text">Codes articles uniquement</div>
                                        <div class="filter-desc">Messages avec un code article</div>
                                    </div>
                                </div>
                                <div class="toggle" :class="{ 'active': filterMode === 'code' }"><div class="toggle-slider"></div></div>
                            </div>

                            <div @click="toggleFilter('phone')" class="filter-option" :class="{ 'active': filterMode === 'phone' }">
                                <div class="filter-left">
                                    <div class="filter-icon" style="background:#dbeafe;color:#1e40af;">
                                        <i class="fas fa-phone-alt"></i>
                                    </div>
                                    <div>
                                        <div class="filter-text">Numéros de téléphone</div>
                                        <div class="filter-desc">Messages avec un numéro</div>
                                    </div>
                                </div>
                                <div class="toggle" :class="{ 'active': filterMode === 'phone' }"><div class="toggle-slider"></div></div>
                            </div>

                            <div @click="toggleFilter('both')" class="filter-option" :class="{ 'active': filterMode === 'both' }">
                                <div class="filter-left">
                                    <div class="filter-icon" style="background:#fed7aa;color:#9a3412;">
                                        <i class="fas fa-crosshairs"></i>
                                    </div>
                                    <div>
                                        <div class="filter-text">Code + Numéro</div>
                                        <div class="filter-desc">Par utilisateur (même si messages séparés)</div>
                                    </div>
                                </div>
                                <div class="toggle" :class="{ 'active': filterMode === 'both' }"><div class="toggle-slider"></div></div>
                            </div>

                            <div @click="toggleFilter('client')" class="filter-option" :class="{ 'active': filterMode === 'client' }">
                                <div class="filter-left">
                                    <div class="filter-icon" style="background:#ede9fe;color:#6d28d9;">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <div>
                                        <div class="filter-text">Fiche Client</div>
                                        <div class="filter-desc">Nom + Code article + Numéro</div>
                                    </div>
                                </div>
                                <div class="toggle" :class="{ 'active': filterMode === 'client' }"><div class="toggle-slider"></div></div>
                            </div>
                        </div>

                        {{-- Configuration Avancée --}}
                        <div class="filter-card">
                            <div class="filter-header">
                                <i class="fas fa-cog" style="font-size:18px;color:#6366f1;"></i>
                                <h3 style="font-weight:600;font-size:14px;margin:0;">Configuration Avancée</h3>
                            </div>

                            <div @click="config.showBasket = !config.showBasket" class="filter-option" :class="{ 'active': config.showBasket }">
                                <div class="filter-left">
                                    <div class="filter-icon" style="background:#f3e8ff;color:#9333ea;">
                                        <i class="fas fa-shopping-bag"></i>
                                    </div>
                                    <div>
                                        <div class="filter-text">Panier par client</div>
                                        <div class="filter-desc">Regrouper par utilisateur</div>
                                    </div>
                                </div>
                                <div class="toggle" :class="{ 'active': config.showBasket }"><div class="toggle-slider"></div></div>
                            </div>

                            <div @click="config.groupQty = !config.groupQty" class="filter-option" :class="{ 'active': config.groupQty }">
                                <div class="filter-left">
                                    <div class="filter-icon" style="background:#e0f2fe;color:#0369a1;">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <div>
                                        <div class="filter-text">Grouper quantités</div>
                                        <div class="filter-desc">Ex: "3x C48"</div>
                                    </div>
                                </div>
                                <div class="toggle" :class="{ 'active': config.groupQty }"><div class="toggle-slider"></div></div>
                            </div>

                            <div @click="config.limitPerArt = !config.limitPerArt" class="filter-option" :class="{ 'active': config.limitPerArt }">
                                <div class="filter-left">
                                    <div class="filter-icon" style="background:#fee2e2;color:#dc2626;">
                                        <i class="fas fa-warehouse"></i>
                                    </div>
                                    <div>
                                        <div class="filter-text">Limite de stock</div>
                                        <div class="filter-desc">
                                            Max <input type="number" x-model="config.maxStock" @click.stop
                                                style="width:35px;border:1px solid #ccc;border-radius:4px;padding:0 2px;"> / article
                                        </div>
                                    </div>
                                </div>
                                <div class="toggle" :class="{ 'active': config.limitPerArt }"><div class="toggle-slider"></div></div>
                            </div>
                        </div>

                    </div>

                    {{-- Right: Comments --}}
                    <div class="comments-card">
                        <div class="comments-tabs">
                            <div class="comments-tab" :class="{ 'tiktok-active': activeComments === 'tiktok' }" @click="activeComments = 'tiktok'">
                                <i class="fab fa-tiktok"></i> TikTok
                            </div>
                            <div class="comments-tab" :class="{ 'facebook-active': activeComments === 'facebook' }" @click="activeComments = 'facebook'">
                                <i class="fab fa-facebook"></i> Facebook
                            </div>
                        </div>

                        <div class="comments-scroll" id="commentsBox">

                            {{-- ✅ FIX : Boucle normale masquée quand limitPerArt est actif --}}
                            <template x-if="!config.limitPerArt">
                                <div>
                                    <template x-for="(item, idx) in displayList" :key="item.user + idx">
                                        <div class="comment-item"
                                             :class="cardClass(item)"
                                             :style="activeComments === 'facebook' ? 'border-left-color:#1877f2' : ''">

                                            {{-- Header --}}
                                            <div class="comment-header">
                                                <span class="comment-author"
                                                      :style="activeComments==='facebook' ? 'color:#1877f2' : 'color:#6366f1'">
                                                    @<span x-text="item.user"></span>
                                                </span>
                                                <div class="comment-badges">
                                                    <span x-show="item.hasCode" class="badge-code">
                                                        <i class="fas fa-box mr-1"></i> CODE
                                                    </span>
                                                    <span x-show="item.hasPhone" class="badge-phone">
                                                        <i class="fas fa-phone-alt mr-1"></i> TEL
                                                    </span>
                                                    <span x-show="(filterMode==='both'||filterMode==='client') && item.hasCode && !item.hasPhone"
                                                          class="badge-pending">
                                                        <i class="fas fa-clock mr-1"></i> Attend numéro
                                                    </span>
                                                    <span x-show="(filterMode==='both'||filterMode==='client') && item.hasPhone && !item.hasCode"
                                                          class="badge-pending">
                                                        <i class="fas fa-clock mr-1"></i> Attend code
                                                    </span>
                                                    <span class="comment-time" x-text="item.time"></span>
                                                </div>
                                            </div>

                                            {{-- Basket mode --}}
                                            <template x-if="config.showBasket">
                                                <div class="comment-body-basket">
                                                    <div class="basket-row user-row">
                                                        <span class="basket-icon"><i class="fas fa-user"></i></span>
                                                        <span class="basket-value username" x-text="item.user"></span>
                                                    </div>
                                                    <template x-for="[code, qty] in Object.entries(item.articles || {})" :key="code">
                                                        <div class="basket-row code-row">
                                                            <span class="basket-icon"><i class="fas fa-tag"></i></span>
                                                            <span class="basket-value code"
                                                                  x-text="qty > 1 ? qty + '× ' + code : code"></span>
                                                            <span x-show="qty > 1" class="qty-badge" x-text="qty + 'x'"></span>
                                                        </div>
                                                    </template>
                                                    <div class="basket-row phone-row">
                                                        <span class="basket-icon"><i class="fas fa-phone-alt"></i></span>
                                                        <span class="basket-value phone" x-text="item.phoneNumber"></span>
                                                        <button class="copy-btn" @click.stop="copyPhone(item.phoneNumber, $event)">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>

                                            {{-- ALL mode --}}
                                            <template x-if="!config.showBasket && filterMode === 'all'">
                                                <div class="comment-body-all" x-text="item.text"></div>
                                            </template>

                                            {{-- CODE mode --}}
                                            <template x-if="!config.showBasket && filterMode === 'code'">
                                                <div class="comment-body-code">
                                                    <i class="fas fa-box" style="font-size:20px;color:#6366f1;"></i>
                                                    <span class="code-label" x-text="item.articleCode"></span>
                                                </div>
                                            </template>

                                            {{-- PHONE mode --}}
                                            <template x-if="!config.showBasket && filterMode === 'phone'">
                                                <div class="comment-body-phone">
                                                    <span class="phone-icon"><i class="fas fa-phone-alt"></i></span>
                                                    <span class="phone-label" x-text="item.phoneNumber"></span>
                                                    <button class="copy-btn" @click.stop="copyPhone(item.phoneNumber, $event)">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                </div>
                                            </template>

                                            {{-- BOTH mode --}}
                                            <template x-if="!config.showBasket && filterMode === 'both'">
                                                <div class="both-wrapper">
                                                    <div class="both-codes-list">
                                                        <span class="both-codes-header">Codes</span>
                                                        <template x-for="[code, qty] in Object.entries(item.articles || {})" :key="code">
                                                            <span class="both-code-chip"
                                                                x-text="config.groupQty && qty > 1 ? qty + '× ' + code : code"></span>
                                                        </template>
                                                    </div>
                                                    <div class="both-divider"></div>
                                                    <div x-show="item.phoneNumber" class="both-phone-row">
                                                        <span class="both-phone-label">Tél</span>
                                                        <span class="both-phone-number" x-text="item.phoneNumber"></span>
                                                        <button class="copy-btn" @click.stop="copyPhone(item.phoneNumber, $event)">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                    <div x-show="!item.phoneNumber" class="both-phone-pending">
                                                        <i class="fas fa-phone-alt"></i>
                                                        <span>En attente du numéro...</span>
                                                    </div>
                                                </div>
                                            </template>

                                            {{-- CLIENT mode --}}
                                            <template x-if="!config.showBasket && filterMode === 'client'">
                                                <div class="client-wrapper">
                                                    <div class="client-header">
                                                        <div class="client-avatar" x-text="item.user.charAt(0).toUpperCase()"></div>
                                                        <span class="client-name" x-text="item.user"></span>
                                                    </div>
                                                    <div class="client-divider"></div>
                                                    <div class="client-codes-list">
                                                        <span class="client-codes-header">Codes</span>
                                                        <template x-for="[code, qty] in Object.entries(item.articles || {})" :key="code">
                                                            <span class="client-code-chip"
                                                                x-text="config.groupQty && qty > 1 ? qty + '× ' + code : code"></span>
                                                        </template>
                                                    </div>
                                                    <div class="client-phone-box">
                                                        <span class="client-phone-icon"><i class="fas fa-phone-alt"></i></span>
                                                        <span class="client-phone-number" x-text="item.phoneNumber"></span>
                                                        <button class="copy-btn" @click.stop="copyPhone(item.phoneNumber, $event)">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>

                                        </div>
                                    </template>

                                    {{-- Message vide --}}
                                    <div x-show="displayList.length === 0" class="empty-comments">
                                        <div class="empty-icon">
                                            <i class="fas fa-comment-slash" style="font-size:40px;color:#cbd5e1;"></i>
                                        </div>
                                        <p x-text="emptyMessage"></p>
                                    </div>
                                </div>
                            </template>

                            {{-- ✅ FIX : Mode limite de stock — boucle indépendante --}}
                            <template x-if="config.limitPerArt">
                                <div>
                                    <template x-for="(article, idx) in stockByArticle" :key="article.code">
                                        <div class="comment-item type-basket">
                                            <div class="comment-header">
                                                <span class="comment-author" style="color:#059669;">
                                                    <i class="fas fa-box mr-1"></i>
                                                    <strong x-text="article.code"></strong>
                                                </span>
                                                <div class="comment-badges">
                                                    <span class="badge-code">
                                                        <i class="fas fa-warehouse mr-1"></i>
                                                        Stock : <span x-text="article.total"></span> / <span x-text="config.maxStock"></span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="comment-body-basket">
                                                <template x-for="(client, ci) in article.clients.filter(c => c.phone)" :key="client.user">
                                                    <div style="display:flex;flex-direction:column;gap:4px;margin-bottom:8px;border-bottom:1px solid #e2e8f0;padding-bottom:8px;">
                                                        <div class="basket-row user-row">
                                                            <span class="basket-icon"><i class="fas fa-user"></i></span>
                                                            <span class="basket-value username" x-text="client.user"></span>
                                                            <span x-show="client.quantity > 1" class="qty-badge" x-text="client.quantity + 'x'"></span>
                                                        </div>
                                                        <div class="basket-row phone-row">
                                                            <span class="basket-icon"><i class="fas fa-phone-alt"></i></span>
                                                            <span class="basket-value phone" x-text="client.phone"></span>
                                                            <button class="copy-btn" @click.stop="copyPhone(client.phone, $event)">
                                                                <i class="fas fa-copy"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Vide --}}
                                    <div x-show="stockByArticle.length === 0" class="empty-comments">
                                        <div class="empty-icon">
                                            <i class="fas fa-box-open" style="font-size:40px;color:#cbd5e1;"></i>
                                        </div>
                                        <p>Aucun article avec stock détecté.</p>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════════ --}}
            {{--             VIEW: Sentiments IA                --}}
            {{-- ══════════════════════════════════════════════ --}}
            <div class="section-panel" :class="{ 'active': view === 'sentiments' }">
                <div style="width:100%;box-sizing:border-box;">

                    <template x-if="!aiAnalysisDone">
                        <div class="ai-empty">
                            <div class="ai-empty__icon">
                                <i class="fas fa-brain" style="font-size:40px;"></i>
                            </div>
                            <p class="ai-empty__text">
                                Aucune analyse disponible.<br>
                                Lancez l'analyse IA depuis le Dashboard.
                            </p>
                        </div>
                    </template>

                    <template x-if="aiAnalysisDone">
                        <div style="display:flex;flex-direction:column;gap:20px;">

                            <div class="sentiment-header">
                                <div>
                                    <h2 class="sentiment-header__title">
                                        <i class="fas fa-brain mr-2" style="color:#8b5cf6;"></i> Analyse des Sentiments
                                    </h2>
                                    <p class="sentiment-header__sub">
                                        <span x-text="sentimentStats.total"></span> commentaires analysés
                                    </p>
                                </div>
                                <span class="sentiment-header__badge">IA · Groq</span>
                            </div>

                            <div class="sentiment-grid">
                                <div class="sentiment-card sentiment-card--positive"
                                    @click="sentimentFilter = sentimentFilter === 'positive' ? 'all' : 'positive'"
                                    :style="sentimentFilter === 'positive' ? 'box-shadow:0 0 0 3px rgba(34,197,94,0.2)' : ''">
                                    <div class="sentiment-card__top">
                                        <i class="fas fa-smile" style="font-size:28px;color:#22c55e;"></i>
                                        <span class="sentiment-card__count sentiment-card__count--positive" x-text="sentimentStats.positive"></span>
                                    </div>
                                    <div class="sentiment-card__label">Positifs</div>
                                    <div class="sentiment-bar sentiment-bar--positive">
                                        <div class="sentiment-bar__fill sentiment-bar__fill--positive"
                                            :style="`width:${sentimentStats.total > 0 ? Math.round((sentimentStats.positive/sentimentStats.total)*100) : 0}%`"></div>
                                    </div>
                                    <div class="sentiment-card__pct sentiment-card__pct--positive"
                                        x-text="`${sentimentStats.total > 0 ? Math.round((sentimentStats.positive/sentimentStats.total)*100) : 0}%`"></div>
                                </div>

                                <div class="sentiment-card sentiment-card--negative"
                                    @click="sentimentFilter = sentimentFilter === 'negative' ? 'all' : 'negative'"
                                    :style="sentimentFilter === 'negative' ? 'box-shadow:0 0 0 3px rgba(239,68,68,0.2)' : ''">
                                    <div class="sentiment-card__top">
                                        <i class="fas fa-angry" style="font-size:28px;color:#ef4444;"></i>
                                        <span class="sentiment-card__count sentiment-card__count--negative" x-text="sentimentStats.negative"></span>
                                    </div>
                                    <div class="sentiment-card__label">Négatifs</div>
                                    <div class="sentiment-bar sentiment-bar--negative">
                                        <div class="sentiment-bar__fill sentiment-bar__fill--negative"
                                            :style="`width:${sentimentStats.total > 0 ? Math.round((sentimentStats.negative/sentimentStats.total)*100) : 0}%`"></div>
                                    </div>
                                    <div class="sentiment-card__pct sentiment-card__pct--negative"
                                        x-text="`${sentimentStats.total > 0 ? Math.round((sentimentStats.negative/sentimentStats.total)*100) : 0}%`"></div>
                                </div>

                                <div class="sentiment-card sentiment-card--neutral"
                                    @click="sentimentFilter = sentimentFilter === 'neutral' ? 'all' : 'neutral'"
                                    :style="sentimentFilter === 'neutral' ? 'box-shadow:0 0 0 3px rgba(148,163,184,0.3)' : ''">
                                    <div class="sentiment-card__top">
                                        <i class="fas fa-meh" style="font-size:28px;color:#94a3b8;"></i>
                                        <span class="sentiment-card__count sentiment-card__count--neutral" x-text="sentimentStats.neutral"></span>
                                    </div>
                                    <div class="sentiment-card__label">Neutres</div>
                                    <div class="sentiment-bar sentiment-bar--neutral">
                                        <div class="sentiment-bar__fill sentiment-bar__fill--neutral"
                                            :style="`width:${sentimentStats.total > 0 ? Math.round((sentimentStats.neutral/sentimentStats.total)*100) : 0}%`"></div>
                                    </div>
                                    <div class="sentiment-card__pct sentiment-card__pct--neutral"
                                        x-text="`${sentimentStats.total > 0 ? Math.round((sentimentStats.neutral/sentimentStats.total)*100) : 0}%`"></div>
                                </div>
                            </div>

                            <div class="sentiment-filters">
                                <span class="sentiment-filters__label">
                                    <i class="fas fa-filter mr-1"></i> Filtrer :
                                </span>
                                <template x-for="f in [
                                    {key:'all',      label:'Tous',      color:'#6366f1'},
                                    {key:'positive', label:'Positifs',  color:'#22c55e'},
                                    {key:'negative', label:'Négatifs',  color:'#ef4444'},
                                    {key:'neutral',  label:'Neutres',   color:'#94a3b8'}
                                ]" :key="f.key">
                                    <button @click="sentimentFilter = f.key"
                                            :style="`border:2px solid ${f.color};
                                                    background:${sentimentFilter === f.key ? f.color : '#fff'};
                                                    color:${sentimentFilter === f.key ? '#fff' : f.color};
                                                    padding:5px 14px;border-radius:999px;
                                                    font-size:12px;font-weight:700;cursor:pointer;transition:all .2s;`"
                                            x-text="f.label">
                                    </button>
                                </template>
                                <span class="sentiment-filters__count"
                                    x-text="`${filteredSentimentComments.length} commentaire(s)`"></span>
                            </div>

                            <div class="sentiment-list">
                                <template x-if="filteredSentimentComments.length === 0">
                                    <div class="sentiment-list__empty">
                                        <i class="fas fa-inbox mr-2"></i> Aucun commentaire dans cette catégorie.
                                    </div>
                                </template>
                                <template x-for="(item, idx) in filteredSentimentComments" :key="idx">
                                    <div class="sentiment-list__item">
                                        <span class="sentiment-list__emoji">
                                            <i :class="item.sentiment === 'positive' ? 'fas fa-smile' :
                                                       item.sentiment === 'negative' ? 'fas fa-angry' : 'fas fa-meh'"
                                               :style="item.sentiment === 'positive' ? 'color:#22c55e' :
                                                       item.sentiment === 'negative' ? 'color:#ef4444' : 'color:#94a3b8'"
                                               style="font-size:22px;"></i>
                                        </span>
                                        <div class="sentiment-list__content">
                                            <div class="sentiment-list__meta">
                                                <span class="sentiment-list__user"
                                                    :class="`sentiment-list__user--${item.sentiment}`"
                                                    x-text="'@' + item.user"></span>
                                                <span class="sentiment-list__badge"
                                                    :class="`sentiment-list__badge--${item.sentiment}`"
                                                    x-text="item.sentiment === 'positive' ? 'Positif' :
                                                            item.sentiment === 'negative' ? 'Négatif' : 'Neutre'"></span>
                                            </div>
                                            <p class="sentiment-list__message" x-text="item.message"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>

                        </div>
                    </template>
                </div>
            </div>

            {{-- VIEW: Codes Articles --}}
            <div class="section-panel" :class="{ 'active': view === 'codes' }">
                <div class="card">
                    @livewire('util.code-article')
                </div>
            </div>

            {{-- VIEW: Historique --}}
            <div class="section-panel" :class="{ 'active': view === 'historique' }">
                <div class="card">
                    @livewire('util.history')
                </div>
            </div>

            {{-- VIEW: Code Stats --}}
            <div class="section-panel" :class="{ 'active': view === 'codestats' }">
                @livewire('util.code-stats')
            </div>

            {{-- VIEW: Paramétrages --}}
            <div class="section-panel" :class="{ 'active': view === 'parametres' }">
                <div class="card">
                    @livewire('util.profile')
                </div>
            </div>

        </div>

        {{-- ══════════════════════════════════════════════ --}}
        {{--             MODAL FIN DE LIVE                  --}}
        {{-- ══════════════════════════════════════════════ --}}
        <div x-show="showEndModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            class="end-modal-overlay">

            <div class="end-modal">

                <div class="end-modal__header">
                    <div>
                        <h2 class="end-modal__title">
                            <i class="fas fa-stop-circle mr-2" style="color:#dc2626;"></i> Fin du Live
                        </h2>
                        <p class="end-modal__date" x-text="endModalDate"></p>
                    </div>
                    <span class="end-modal__platform-badge"
                        :class="platform === 'facebook'
                            ? 'end-modal__platform-badge--facebook'
                            : 'end-modal__platform-badge--tiktok'">
                        <i :class="platform === 'facebook' ? 'fab fa-facebook mr-1' : 'fab fa-tiktok mr-1'"></i>
                        <span x-text="platform === 'facebook' ? 'Facebook' : 'TikTok'"></span>
                    </span>
                </div>

                <div class="end-modal__stats">
                    <div class="end-modal__stat end-modal__stat--comments">
                        <div class="end-modal__stat-value" x-text="stats.comments"></div>
                        <div class="end-modal__stat-label">
                            <i class="fas fa-comments mr-1"></i> Commentaires
                        </div>
                    </div>
                    <div class="end-modal__stat end-modal__stat--clients">
                        <div class="end-modal__stat-value" x-text="stats.clients"></div>
                        <div class="end-modal__stat-label">
                            <i class="fas fa-users mr-1"></i> Clients
                        </div>
                    </div>
                    <div class="end-modal__stat end-modal__stat--articles">
                        <div class="end-modal__stat-value" x-text="stats.articles"></div>
                        <div class="end-modal__stat-label">
                            <i class="fas fa-box mr-1"></i> Articles
                        </div>
                    </div>
                    <div class="end-modal__stat end-modal__stat--phones">
                        <div class="end-modal__stat-value" x-text="stats.phones"></div>
                        <div class="end-modal__stat-label">
                            <i class="fas fa-phone-alt mr-1"></i> Numéros
                        </div>
                    </div>
                </div>

                <div class="end-modal__baskets">
                    <h3 class="end-modal__baskets-title">
                        <i class="fas fa-shopping-cart mr-2" style="color:#6366f1;"></i> Paniers clients
                    </h3>
                    <div class="end-modal__baskets-list">
                        <template x-if="endBaskets.length === 0">
                            <div class="end-modal__baskets-empty">
                                <i class="fas fa-inbox mr-2"></i> Aucun panier détecté
                            </div>
                        </template>
                        <template x-for="(basket, i) in endBaskets" :key="i">
                            <div class="basket-card">
                                <div class="basket-card__header">
                                    <i class="fas fa-shopping-cart mr-1"></i>
                                    Panier #<span x-text="i+1"></span> —
                                    <span class="basket-card__client" x-text="basket.client"></span>
                                </div>
                                <div class="basket-card__body">
                                    <template x-for="(art, ai) in basket.articles" :key="ai">
                                        <div class="basket-card__article">
                                            <i class="fas fa-tag"></i>
                                            <span class="basket-card__article-text" x-text="art"></span>
                                        </div>
                                    </template>
                                    <template x-for="(phone, pi) in basket.phones" :key="pi">
                                        <div class="basket-card__phone">
                                            <i class="fas fa-phone-alt"></i>
                                            <span class="basket-card__phone-text" x-text="phone"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <button @click="downloadCSV()"
                            style="flex:1;padding:12px;border-radius:10px;border:1px solid #e2e8f0;background:#f8fafc;font-weight:600;font-size:14px;cursor:pointer;">
                        <i class="fas fa-file-csv mr-1" style="color:#10b981;"></i> Télécharger CSV
                    </button>
                    <button @click="downloadTXT()"
                            style="flex:1;padding:12px;border-radius:10px;border:1px solid #e2e8f0;background:#f8fafc;font-weight:600;font-size:14px;cursor:pointer;">
                        <i class="fas fa-file-alt mr-1" style="color:#6366f1;"></i> Télécharger TXT
                    </button>
                    <button @click="cancelEndModal()"
                            style="flex:1;padding:12px;border-radius:10px;border:1px solid #e2e8f0;background:#fff;font-weight:600;font-size:14px;cursor:pointer;color:#64748b;">
                        <i class="fas fa-times mr-1"></i> Annuler
                    </button>
                    <button @click="confirmEndLive()"
                            :disabled="saving"
                            style="flex:1;padding:12px;border-radius:10px;border:none;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;font-weight:600;font-size:14px;cursor:pointer;">
                        <span x-show="!saving">
                            <i class="fas fa-check-circle mr-1"></i> Confirmer et terminer
                        </span>
                        <span x-show="saving">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Enregistrement...
                        </span>
                    </button>
                </div>
            </div>
        </div>

    </main>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        window.pusherAppKey = '{{ env('PUSHER_APP_KEY') }}';
        window.pusherCluster = '{{ env('PUSHER_APP_CLUSTER', 'mt1') }}';
        window.codeArticles = @json($codesArticles ?? []);
        document.addEventListener('alpine:init', () => {
            Alpine.store('live', { isLive: false });
        });
    </script>
    <script src="{{ asset('js/commentaire/dashboard.js') }}"></script>
@endpush

@endsection