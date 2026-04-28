<?php

namespace App\Traits;

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait SubscriptionLimits
{
    // ══════════════════════════════════════════
    //  Limites par plan
    // ══════════════════════════════════════════
    protected array $planLimits = [
        'Starter' => [
            'lives_per_month'    => 5,
            'max_comments'       => 200,
            'history_days'       => 7,
            'max_accounts'       => 1,
        ],
        'Business' => [
            'lives_per_month'    => PHP_INT_MAX,
            'max_comments'       => 1000,
            'history_days'       => 30,
            'max_accounts'       => 1,
        ],
        'Enterprise' => [
            'lives_per_month'    => PHP_INT_MAX,
            'max_comments'       => PHP_INT_MAX,
            'history_days'       => PHP_INT_MAX,
            'max_accounts'       => PHP_INT_MAX,
        ],
    ];

    // ══════════════════════════════════════════
    //  Récupérer l'abonnement actif
    // ══════════════════════════════════════════
    protected function getActivePayment(): ?Payment
{
    return Payment::where('user_id', Auth::id())
        ->where('status', 'accepte')
        ->where(function($query) {
            $query->where('expires_at', '>=', now())
                  ->orWhereNull('expires_at');
        })
        ->latest()
        ->first();
}

    // ══════════════════════════════════════════
    //  Récupérer les limites du plan actuel
    // ══════════════════════════════════════════
    protected function getPlanLimits(string $planName): array
    {
        return $this->planLimits[$planName] ?? $this->planLimits['Starter'];
    }

    // ══════════════════════════════════════════
    //  Vérifier abonnement + limites avant start
    //  $table = 'live_sessions' ou 'facebook_sessions'
    // ══════════════════════════════════════════
    protected function checkLiveLimit(string $table): ?array
    {
        // 1. Vérifier abonnement actif
        $payment = $this->getActivePayment();

        if (!$payment) {
            return [
                'error'    => 'Abonnement requis',
                'message'  => '⚠️ Votre abonnement est expiré ou inexistant.',
                'redirect' => route('pricing'),
                'code'     => 403,
            ];
        }

        $plan   = $payment->plan_name;
        $limits = $this->getPlanLimits($plan);

        // 2. Vérifier limite lives/mois (Starter seulement)
        if ($limits['lives_per_month'] !== PHP_INT_MAX) {
            $livesThisMonth = DB::table($table)
                ->where('user_id', Auth::id())
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at',  now()->year)
                ->count();

            if ($livesThisMonth >= $limits['lives_per_month']) {
                return [
                    'error'    => 'Limite atteinte',
                    'message'  => "⚠️ Plan {$plan} : {$livesThisMonth}/{$limits['lives_per_month']} lives utilisés ce mois-ci. Passez à Business pour des lives illimités.",
                    'redirect' => route('pricing'),
                    'code'     => 403,
                ];
            }
        }

        // ✅ Tout bon — retourner les infos utiles
        return null;
    }

    // ══════════════════════════════════════════
    //  Vérifier limite commentaires en cours de live
    // ══════════════════════════════════════════
    protected function checkCommentLimit(int $currentCount): ?array
    {
        $payment = $this->getActivePayment();
        if (!$payment) return null;

        $limits = $this->getPlanLimits($payment->plan_name);

        if ($limits['max_comments'] !== PHP_INT_MAX && $currentCount >= $limits['max_comments']) {
            return [
                'error'   => 'Limite commentaires',
                'message' => "⚠️ Plan {$payment->plan_name} : limite de {$limits['max_comments']} commentaires atteinte.",
                'limit'   => $limits['max_comments'],
                'code'    => 403,
            ];
        }

        return null;
    }

    // ══════════════════════════════════════════
    //  Appliquer filtre historique selon le plan
    // ══════════════════════════════════════════
    protected function applyHistoryFilter($query, string $table): mixed
    {
        $payment = $this->getActivePayment();
        if (!$payment) return $query;

        $limits = $this->getPlanLimits($payment->plan_name);

        if ($limits['history_days'] !== PHP_INT_MAX) {
            $query->where("{$table}.created_at", '>=',
                now()->subDays($limits['history_days']));
        }

        return $query;
    }
}