@extends('layouts.app')

@section('title', 'Paiement en attente')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/billing/pending.css') }}">
@endpush

@section('body-class', 'min-h-screen flex items-center justify-center py-10 px-4')

@section('content')
<div class="w-full max-w-2xl mx-auto fade-in">

    {{-- ===== ANIMATION HORLOGE ===== --}}
    <div class="flex justify-center mb-8">
        <div class="relative w-32 h-32">
            <div class="absolute inset-0 bg-yellow-500/20 rounded-full pulse-animation"></div>
            <div class="relative w-32 h-32 bg-gradient-to-br from-yellow-500/30 to-yellow-600/30 rounded-full flex items-center justify-center backdrop-blur-sm border border-yellow-500/30">
                <svg class="w-16 h-16 text-yellow-500 spinner" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- ===== MESSAGE PRINCIPAL ===== --}}
    <div class="glass-card rounded-3xl p-8 shadow-2xl mb-6 text-center">
        <h1 class="text-3xl font-bold mb-3">Paiement en cours de validation</h1>
        <p class="text-gray-400 text-lg">Votre demande d'abonnement a bien été reçue</p>
    </div>

    {{-- ===== DÉTAILS PAIEMENT ===== --}}
    @if($payment)
    <div class="glass-card rounded-2xl p-6 mb-6 border border-white/10">
        <h3 class="text-sm font-semibold text-gray-400 uppercase mb-4 tracking-wider">Détails de votre demande</h3>
        <div class="space-y-4">
            <div class="flex justify-between items-center border-b border-white/10 pb-3">
                <span class="text-gray-400">Plan sélectionné</span>
                <span class="font-bold text-xl text-white">{{ $payment->plan_name }}</span>
            </div>
            <div class="flex justify-between items-center border-b border-white/10 pb-3">
                <span class="text-gray-400">Montant</span>
                <span class="font-bold text-2xl text-indigo-400">{{ $payment->amount }} TND</span>
            </div>
            <div class="flex justify-between items-center border-b border-white/10 pb-3">
                <span class="text-gray-400">Statut</span>
                <span class="px-4 py-2 bg-yellow-500/20 text-yellow-400 rounded-full text-sm font-bold uppercase tracking-wider">
                    ⏳ En attente
                </span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-400">Date de demande</span>
                <span class="text-white font-semibold">{{ $payment->created_at->format('d/m/Y à H:i') }}</span>
            </div>
        </div>
    </div>
    @endif

    {{-- ===== INFOS SUPPLÉMENTAIRES ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-blue-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <div>
                    <h4 class="font-semibold text-blue-300 mb-1">Notification automatique</h4>
                    <p class="text-xs text-gray-400">Vous recevrez une notification dès validation</p>
                </div>
            </div>
        </div>

        <div class="bg-green-500/10 border border-green-500/20 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-green-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="font-semibold text-green-300 mb-1">Délai de traitement</h4>
                    <p class="text-xs text-gray-400">Généralement entre 5 et 30 minutes</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== COMPTEUR AUTO-REFRESH ===== --}}
    <div class="bg-indigo-500/10 border border-indigo-500/20 rounded-xl p-4 text-center mb-6">
        <p class="text-sm text-gray-300">
            🔄 Actualisation automatique dans
            <span id="countdown" class="font-bold text-indigo-400">30</span> secondes
        </p>
        <div class="mt-2 w-full bg-white/10 rounded-full h-1 overflow-hidden">
            <div id="progress" class="bg-indigo-500 h-full rounded-full transition-all duration-1000" style="width: 100%"></div>
        </div>
    </div>

    {{-- ===== BOUTONS ===== --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-8">
        <a href="{{ route('selection') }}"
            class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 py-4 rounded-2xl font-bold text-center hover:shadow-lg hover:shadow-indigo-500/20 transition">
            Retour au page de selection
        </a>
        <button onclick="window.location.reload()"
            class="flex-1 bg-white/5 border border-white/10 py-4 rounded-2xl font-semibold hover:bg-white/10 transition flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Actualiser maintenant
        </button>
    </div>

    {{-- ===== SUPPORT ===== --}}
    <div class="text-center">
        <p class="text-gray-500 text-sm mb-2">💡 Besoin d'aide ? Contactez notre support</p>
        <a href="mailto:support@tiktokfrip.com" class="text-indigo-400 hover:text-indigo-300 text-sm font-semibold transition">
            support@tiktokfrip.com
        </a>
    </div>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/billing/pending.js') }}"></script>
@endpush