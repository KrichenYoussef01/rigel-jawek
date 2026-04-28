@extends('layouts.app')

@section('title', 'Abonnements')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/billing/pricing.css') }}">
@endpush

@section('content')
<div class="text-center mb-16">
    <h1 class="text-4xl font-bold mb-4">Choisissez votre abonnement</h1>
    <p class="text-gray-400">Prix en Dinars Tunisiens (TND) • Sans engagement</p>
</div>

<div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">

    @foreach($plans as $plan)
    @php
        $slug       = strtolower($plan->plan_name); // starter, business, enterprise
        $isPopular  = $plan->plan_name === 'Business';
        $colors     = [
            'Starter'    => ['bg' => 'bg-emerald-600', 'btn' => 'bg-emerald-700 hover:bg-emerald-600', 'icon' => '⚡'],
            'Business'   => ['bg' => 'bg-indigo-600',  'btn' => 'bg-indigo-600 hover:bg-indigo-500',   'icon' => '👑'],
            'Enterprise' => ['bg' => 'bg-orange-500',  'btn' => 'bg-orange-600 hover:bg-orange-500',   'icon' => '🚀'],
        ];
        $color = $colors[$plan->plan_name] ?? $colors['Starter'];
    @endphp

    <div class="pricing-card {{ $isPopular ? 'popular-card' : '' }} p-8 rounded-3xl relative">

        @if($isPopular)
            <div class="badge">Plus populaire</div>
        @endif

        <div class="{{ $color['bg'] }} w-12 h-12 rounded-lg flex items-center justify-center mb-6">
            <span class="text-2xl">{{ $color['icon'] }}</span>
        </div>

        <h3 class="text-xl font-bold mb-2">{{ $plan->plan_name }}</h3>

        <div class="text-4xl font-black mb-6">
            {{ $plan->prix }} <span class="text-sm font-normal text-gray-400">TND/mois</span>
        </div>

        <ul class="space-y-4 mb-8 text-gray-300">
            <li>{{ $plan->max_lives_par_mois ? '✅ '.$plan->max_lives_par_mois.' lives / mois' : '✅ Lives illimités' }}</li>
            <li>{{ $plan->max_commandes_par_mois ? '✅ '.$plan->max_commandes_par_mois.' commandes / mois' : '✅ Commandes illimitées' }}</li>
            <li>{{ $plan->max_comptes_tiktok ? '✅ '.$plan->max_comptes_tiktok.' comptes TikTok' : '✅ Comptes TikTok illimités' }}</li>
            <li>
                @if($plan->support_prioritaire)
                    ✅ Support prioritaire
                @else
                    <span class="text-gray-600">❌ Support prioritaire</span>
                @endif
            </li>
            @if($plan->api_personnalisee)
                <li>✅ API personnalisée</li>
            @endif
            @if($plan->manager_de_compte)
                <li>✅ Manager de compte</li>
            @endif
        </ul>

        <a href="{{ route('checkout', ['plan' => $slug]) }}"
           class="block text-center w-full py-4 rounded-2xl {{ $color['btn'] }} font-bold transition text-white">
            Commencer
        </a>

    </div>
    @endforeach

</div>
@endsection