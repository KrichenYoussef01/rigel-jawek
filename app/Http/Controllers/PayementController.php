<?php

namespace App\Http\Controllers;
use App\Models\CompanyWallet;
use App\Models\Payment;
use App\Models\UsageCounter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayementController extends Controller
{
  
        public function restore($id)
{
    $payment = Payment::findOrFail($id);

  
    if ($payment->status !== 'suspendu') {
        return back()->with('error', 'Ce paiement ne peut pas être réactivé (statut incorrect).');
    }

    
    $payment->status = 'accepte';
    $payment->is_paid = true;
    $payment->expires_at = now()->addMonth(); 

    $payment->save();


    return back()->with('success', 'Le paiement a été accepté et le compte de l\'utilisateur est réactivé.');
}
      
   


   
    public function accept($id)
{
    
    $payment = Payment::findOrFail($id);
    $payment->update(['status' => 'accepte','expires_at' => now()->addDays(30)]);
    
    
    $wallet = CompanyWallet::firstOrCreate(['id' => 1]);
    $wallet->increment('balance', $payment->amount);
    
    return back()->with('success', 'Le paiement a été marqué comme accepté.');
    }


    public function refuse($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->update(['status' => 'refuse']);

        return back()->with('error', 'Le paiement a été décliné.');
    }

  


    public function store(Request $request)
{
    $user = Auth::id();

    $hasCardData   = !empty($request->card_number) && !empty($request->cvv);
    $paymentMethod = $hasCardData ? 'carte' : 'especes';
    $numericPrice  = (float) str_replace(' TND', '', $request->amount);

    // Expirer l'ancien abonnement actif
    Payment::where('user_id', $user)
        ->where('status', 'accepte')
        ->where('expires_at', '>=', now())
        ->update(['expires_at' => now()]);

    // Bloquer si un paiement est déjà en attente ou suspendu
    $pendingPayment = Payment::where('user_id', $user)
        ->whereIn('status', ['en_attente', 'suspendu'])
        ->first();

    if ($pendingPayment) {
        $message = $pendingPayment->status === 'suspendu'
            ? 'Votre compte est suspendu. Contactez l\'administrateur.'
            : 'Vous avez déjà une demande en attente.';
        return redirect()->route('payment.pending')->with('info', $message);
    }

    // Déterminer le statut selon le moyen de paiement
    if ($paymentMethod === 'carte') {
        $status    = 'accepte';
        $isPaid    = true;
        $expiresAt = now()->addMonth();
    } else {
        $status    = 'suspendu';
        $isPaid    = false;
        $expiresAt = null;
    }

    // Créer le nouvel abonnement
    Payment::create([
        'user_id'        => $user,
        'plan_name'      => $request->plan_name,
        'amount'         => $numericPrice,
        'payment_method' => $paymentMethod,
        'is_paid'        => $isPaid,
        'status'         => $status,
        'expires_at'     => $expiresAt,
    ]);

    // Réinitialisation des compteurs pour un paiement accepté (carte)
    if ($status === 'accepte') {
        $mois = now()->format('Y-m');

        // Vérifier si un compteur existe déjà pour ce mois
        $counter = UsageCounter::where('user_id', $user)
            ->where('mois', $mois)
            ->first();

        if ($counter) {
            // Mise à jour de l'enregistrement existant
            $counter->update([
                'nb_lives_utilises'        => 0,
                'nb_commandes_utilises'    => 0,
                'nb_exports_utilises'      => 0,
                'nb_commentaires_utilises' => 0,
            ]);
        } else {
            // Création d'un nouvel enregistrement
            UsageCounter::create([
                'user_id'                  => $user,
                'mois'                     => $mois,
                'nb_lives_utilises'        => 0,
                'nb_commandes_utilises'    => 0,
                'nb_exports_utilises'      => 0,
                'nb_commentaires_utilises' => 0,
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Abonnement renouvelé avec succès !');
    }

    // Cas du paiement en espèces (en attente)
    return redirect()->route('payment.suspended')
        ->with('warning', 'Paiement en attente de validation.');
}
public function togglePaid(Payment $payment)
{
    $payment->update(['is_paid' => !$payment->is_paid]);
    return back()->with('success', 'Statut paiement mis à jour.');
}

public function approve(Request $request, Payment $payment)
{
    $payment->update([
        'status'     => 'accepte',
        'is_paid'    => true,
        'expires_at' => now()->addMonth(),
    ]);

    // ✅ Créer automatiquement le compteur du mois
    \App\Models\UsageCounter::firstOrCreate(
        [
            'user_id' => $payment->user_id,
            'mois'    => now()->format('Y-m'),
        ],
        [
            'nb_lives_utilises'        => 0,
            'nb_commandes_utilises'    => 0,
            'nb_commentaires_utilises' => 0,
            'nb_exports_utilises'      => 0,
        ]
    );

    return redirect()->back()->with('success', 'Paiement approuvé et compteur créé.');
}



public function getLimits()
{
    $userId   = Auth::id();
    $payment  = \App\Models\Payment::where('user_id', $userId)
        ->where('status', 'accepte')
        ->where('expires_at', '>', now())
        ->latest()->first();

    if (!$payment) {
        return response()->json(['error' => 'Aucun abonnement actif'], 403);
    }

    $limites  = \App\Models\PlanLimit::where('plan_name', $payment->plan_name)->first();
    $compteur = \App\Models\UsageCounter::firstOrCreate(
        ['user_id' => $userId, 'mois' => now()->format('Y-m')],
        ['nb_lives_utilises' => 0, 'nb_commandes_utilises' => 0, 'nb_commentaires_utilises' => 0, 'nb_exports_utilises' => 0]
    );

    return response()->json([
        'plan'       => $payment->plan_name,
        'expire_le'  => $payment->expires_at,
        'lives'      => [
            'utilises' => $compteur->nb_lives_utilises,
            'limite'   => $limites->max_lives_par_mois,     // null = illimité
        ],
        'commandes'  => [
            'utilises' => $compteur->nb_commandes_utilises,
            'limite'   => $limites->max_commandes_par_mois,
        ],
        'exports'    => [
            'utilises' => $compteur->nb_exports_utilises,
            'limite'   => $limites->max_exports_par_jour,
        ],
        'fonctionnalites' => [
            'paniers_automatiques'  => $limites->paniers_automatiques,
            'extraction_temps_reel' => $limites->extraction_temps_reel,
            'multi_comptes_tiktok'  => $limites->multi_comptes_tiktok,
            'api_personnalisee'     => $limites->api_personnalisee,
        ],
    ]);
}
}
