<?php

namespace App\Livewire\Util;

use App\Mail\PasswordChanged;
use App\Models\Payment;
use App\Models\PlanLimit;
use App\Models\UsageCounter;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class Profile extends Component
{
    public string $activeTab = 'profil';
    public string $name = '';
    public string $email = '';
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    // Abonnement
    public ?int $daysLeft = 0;
    public ?int $daysUsed = 0;      // 🔥 ajouté
    public ?string $planName = null;
    public ?string $planStatus = null;
    public ?string $planStart = null;
    public ?string $planEnd = null;
    public int $totalLives = 0;
    public int $totalArticles = 0;

    public ?string $successMessage = null;
    public ?string $errorMessage = null;

    public ?int $limitLives = null;
    public ?int $limitCommandes = null;
    public ?int $limitExports = null;
    public int $totalCommandes = 0;
    public int $totalExports = 0;

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;

        // Récupérer le dernier paiement accepté
        $payment = Payment::where('user_id', $user->id)
            ->where('status', 'accepte')
            ->latest()
            ->first();

        if ($payment) {
            $this->planName = $payment->plan_name ?? 'Standard';
            $this->planStatus = $payment->status;
            $this->planStart = $payment->created_at->format('d/m/Y');
            $this->planEnd = $payment->expires_at ? $payment->expires_at->format('d/m/Y') : $payment->created_at->addMonth()->format('d/m/Y');

        
            // Récupération des limites du plan
            $planLimit = PlanLimit::where('plan_name', $payment->plan_name)->first();
            if ($planLimit) {
                $this->limitLives = $planLimit->max_lives_par_mois;
                $this->limitCommandes = $planLimit->max_commandes_par_mois;
                $this->limitExports = $planLimit->max_exports_par_mois ?? null;
            }
        }

        // Compteurs du mois
        $mois = now()->format('Y-m');
        $counter = UsageCounter::where('user_id', $user->id)->where('mois', $mois)->first();
        if ($counter) {
            $this->totalLives = $counter->nb_lives_utilises ?? 0;
            $this->totalCommandes = $counter->nb_commandes_utilises ?? 0;
            $this->totalExports = $counter->nb_exports_utilises ?? 0;
            $this->totalArticles = $counter->nb_commentaires_utilises ?? 0;
        }
    }

    // 🔥 Méthode flexible pour les jours restants
    private function calculateDaysLeft(): int
    {
        $lastPayment = Payment::where('user_id', Auth::id())
            ->where('status', 'accepte')
            ->latest()
            ->first();
        if (!$lastPayment) {
            return 0;
        }
        // Utiliser expires_at s'il existe, sinon created_at + 30 jours
        $expiryDate = $lastPayment->expires_at
            ? $lastPayment->expires_at->copy()->startOfDay()
            : $lastPayment->created_at->copy()->addDays(30)->startOfDay();
        $today = now()->startOfDay();
        $diff = $today->diffInDays($expiryDate, false);
        return $diff > 0 ? $diff : 0;
    }

    // 🔥 Méthode pour les jours utilisés
    private function calculateDaysUsed(): int
    {
        $lastPayment = Payment::where('user_id', Auth::id())
            ->where('status', 'accepte')
            ->latest()
            ->first();
        if (!$lastPayment) {
            return 0;
        }
        $startDate = $lastPayment->created_at->copy()->startOfDay();
        $today = now()->startOfDay();
        if ($startDate->greaterThan($today)) {
            return 0;
        }
        return $startDate->diffInDays($today);
    }

    // ─── setTab, updateProfil, updatePassword (inchangés) ───
    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->successMessage = null;
        $this->errorMessage = null;
    }

    public function updateProfil(): void
    {
        $this->validate([
            'name' => 'required|min:2|max:100',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);
        User::find(Auth::id())->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);
        $this->successMessage = 'Profil mis à jour avec succès !';
        $this->errorMessage = null;
    }

    public function updatePassword(): void
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
            'new_password_confirmation' => 'required',
        ]);
        if (!Hash::check($this->current_password, Auth::user()->password)) {
            $this->errorMessage = 'Le mot de passe actuel est incorrect.';
            $this->successMessage = null;
            return;
        }
        User::find(Auth::id())->update([
            'password' => Hash::make($this->new_password),
        ]);
        Mail::to(Auth::user()->email)->send(new PasswordChanged(Auth::user()));
        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';
        $this->successMessage = 'Mot de passe changé avec succès ! Un email de confirmation vous a été envoyé.';
        $this->errorMessage = null;
    }

    public function render()
    {
         // 🔥 Calcul des jours restants (méthode flexible)
            $this->daysLeft = $this->calculateDaysLeft();
            $this->daysUsed = $this->calculateDaysUsed();
        return view('livewire.util.profile');
    }
}