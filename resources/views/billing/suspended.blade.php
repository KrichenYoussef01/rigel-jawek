@extends('layouts.app')

@section('title', 'Compte suspendu')

@section('content')
<div class="max-w-2xl mx-auto mt-16 p-8 bg-white rounded-2xl shadow text-center">
    <div class="text-6xl mb-4">⏳</div>
    <h1 class="text-3xl font-bold text-gray-800 mb-4">Compte en attente de validation</h1>
    <p class="text-gray-600 mb-6">
        Votre demande d'abonnement (plan <strong>{{ $payment->plan_name }}</strong>) a été enregistrée.<br>
        Votre compte est actuellement <span class="font-semibold text-orange-600">suspendu</span> en attendant la confirmation de votre paiement en espèces.
    </p>
    <p class="text-gray-500 text-sm mb-8">
        Veuillez contacter l'administrateur pour finaliser votre inscription. Vous recevrez une notification dès que votre compte sera activé.
    </p>
    <a href="{{ route('logout') }}" class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        Se déconnecter
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
</div>
@endsection