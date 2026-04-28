@extends('layouts.app')

@section('title', 'Paiement')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/billing/checkout.css') }}">
@endpush

@section('body-class', 'min-h-screen flex items-center justify-center py-10 bg-gray-50')

@section('content')
@php
    $planLimits = \App\Models\PlanLimit::where('plan_name', $planName)->first();
    $orderNumber = rand(1000000, 9999999);
@endphp
 
<div class="checkout-wrapper">
 
    {{-- Logo --}}
    <div class="checkout-logo-bar">
        <div style="font-size:1.3rem;font-weight:800;color:#1d4ed8;letter-spacing:-0.02em;">
            DKSoft <span style="font-weight:400;color:#6b7280;font-size:0.9rem;">by Monétique</span>
        </div>
    </div>
 
    <div class="checkout-card">
 
        {{-- Header --}}
        <div class="checkout-header">
            <div class="checkout-order-label">Numéro de la commande</div>
            <div class="checkout-order-number">№{{ $orderNumber }}</div>
            <div class="checkout-timer">
                Il reste jusqu'à la fin de la session <strong id="countdown">14 min. 59 sec.</strong>
            </div>
        </div>
 
        {{-- Amount bar --}}
        <div class="checkout-amount-bar">
            <span>Plan {{ $planName }}</span>
            <span class="amount-value">{{ $planPrice }}</span>
        </div>
 
        {{-- Body --}}
        <div class="checkout-body">
 
            {{-- Plan features --}}
            @if($planLimits)
            <div class="plan-info-block">
                <div class="plan-name-badge">{{ $planName }}</div>
                <ul class="plan-features-list">
                    <li>⚡ {{ $planLimits->max_lives_par_mois ?? '∞' }} lives/mois</li>
                    <li>📦 {{ $planLimits->max_commandes_par_mois ?? '∞' }} commandes</li>
                    <li>📱 {{ $planLimits->max_comptes_tiktok ?? '∞' }} comptes TikTok</li>
                    @if($planLimits->support_prioritaire) <li>🛟 Support prioritaire</li> @endif
                    @if($planLimits->api_personnalisee)     <li>🔌 API personnalisée</li>  @endif
                    @if($planLimits->manager_de_compte)     <li>👤 Manager de compte</li> @endif
                </ul>
            </div>
            @endif
 
            <div class="token-row">🔐 Token : <span id="displayToken">Génération...</span></div>
 
            {{-- Form --}}
            <form id="paymentForm" action="{{ route('payment.process') }}" method="POST">
                @csrf
                <input type="hidden" name="payment_token"  id="paymentToken">
                <input type="hidden" name="payment_method" id="paymentMethod" value="carte">
                <input type="hidden" name="plan_name"      value="{{ $planName }}">
                <input type="hidden" name="amount"         value="{{ $planPrice }}">
 
                {{-- Card number --}}
                <div class="form-group" id="cardNumberGroup">
                    <label>Numéro de la carte</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <rect x="2" y="5" width="20" height="14" rx="2" stroke-width="2"/>
                                <path d="M2 10h20" stroke-width="2"/>
                            </svg>
                        </span>
                        <input type="text" name="card_number" id="cardNumber"
                               placeholder="0000 0000 0000 0000"
                               class="card-number-input"
                               maxlength="19" autocomplete="off">
                    </div>
                </div>
 
                {{-- Mois / Année / CVV --}}
                <div class="form-row" id="cardFields">
                    <div class="form-group" style="margin:0">
                        <label>Mois</label>
                        <div class="input-wrap">
                            <select name="expiry_month" id="expiryMonth">
                                <option value="">Mois</option>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ str_pad($m,2,'0',STR_PAD_LEFT) }}">{{ str_pad($m,2,'0',STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="form-group" style="margin:0">
                        <label>Année</label>
                        <div class="input-wrap">
                            <select name="expiry_year" id="expiryYear">
                                <option value="">Année</option>
                                @for($y = date('Y'); $y <= date('Y')+10; $y++)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="form-group" style="margin:0">
                        <label>Code de sûreté</label>
                        <div class="input-wrap">
                            <input type="password" name="cvv" id="cvvInput"
                                   placeholder="•••" maxlength="4" autocomplete="off">
                        </div>
                    </div>
                </div>
 
                {{-- Nom --}}
                <div class="form-group" id="cardNameGroup">
                    <label>Le nom du détenteur</label>
                    <div class="input-wrap">
                        <input type="text" name="card_name" id="cardName"
                               placeholder="Prénom et Nom" autocomplete="off">
                    </div>
                </div>
 
                {{-- Email --}}
                <div class="email-toggle">
                    <input type="checkbox" id="showEmail" checked>
                    <label for="showEmail">Adresse e-mail</label>
                </div>
                <div class="form-group" id="emailGroup">
                    <div class="input-wrap">
                        <input type="email" name="email"
                               placeholder="votre@email.com" autocomplete="email">
                    </div>
                </div>
 
                {{-- Cash --}}
                <div class="cash-mode-row">
                    <input type="checkbox" id="cashPayment" onchange="toggleCashMode()">
                    <label for="cashPayment">💵 Payer en espèces <span style="color:#9ca3af;font-size:0.75rem;">(aucune carte requise)</span></label>
                </div>
 
                <div class="cash-alert" id="cashAlert">
                    💵 Mode espèces — Aucune donnée bancaire requise
                </div>
 
                <button type="submit" class="btn-pay" id="submitBtn">
                    Paiement {{ $planPrice }}
                </button>
            </form>
        </div>
 
        {{-- Footer --}}
        <div class="checkout-footer">
            <span class="secure-label">
                <i class="fas fa-lock"></i> Paiement sécurisé
            </span>
            <div class="card-logos">
                <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Mastercard-logo.png" alt="Mastercard">
                <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png" alt="Visa">
            </div>
        </div>
 
    </div>
</div>
@endsection
 
@push('scripts')
<script src="{{ asset('js/billing/checkout.js') }}"></script>