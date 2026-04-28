<?php

namespace App\Livewire\Admin;

use App\Models\Payment;
use App\Models\PlanLimit;
use App\Models\User;
use App\Models\UsageCounter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Settings extends Component
{
    // ─── Onglet actif ────────────────────────────────────
    public string $activeTab = 'profil';
    public $editPrix;
    // ─── Profil ──────────────────────────────────────────
    public string $name  = '';
    public string $email = '';

    // ─── Mot de passe ────────────────────────────────────
    public string $new_password             = '';
    public string $new_password_confirmation = '';

    // ─── Abonnement ──────────────────────────────────────
    public ?int    $daysLeft   = 0;
    public ?string $planName   = null;
    public ?string $planStatus = null;
    public ?string $planStart  = null;
    public ?string $planEnd    = null;

    // ─── Compteurs d'utilisation ─────────────────────────
    public int $totalLives     = 0;
    public int $totalArticles  = 0;
    public int $totalCommandes = 0;
    public int $totalExports   = 0;

    // ─── Limites du plan ─────────────────────────────────
    public ?int $limitLives     = null;
    public ?int $limitCommandes = null;
    public ?int $limitExports   = null;

    // ─── Liste des plans ─────────────────────────────────
    public $plans = [];

    // ─── Modal détails plan ──────────────────────────────
    public bool $showPlanDetailsModal = false;
    public ?int $selectedPlanId       = null;

    // ─── Modal édition plan ──────────────────────────────
    public bool    $showEditModal          = false;
    public ?int    $editId                 = null;
    public string  $editPlanName           = '';
    public ?string $editMaxLives           = '';
    public ?string $editMaxCommandes       = '';
    public ?string $editMaxComptesTiktok   = '';
    public bool    $editSupportPrioritaire = false;
    public bool    $editApiPersonnalisee   = false;
    public bool    $editManagerDeCompte    = false;

    // ─── Feedback ────────────────────────────────────────
    public ?string $successMessage = null;
    public ?string $errorMessage   = null;

    // ─────────────────────────────────────────────────────
    public function mount(): void
    {
        $user = Auth::user();

        // Profil
        $this->name  = $user->name;
        $this->email = $user->email;

        // Abonnement
        $payment = Payment::where('user_id', $user->id)
            ->where('status', 'accepte')
            ->latest()
            ->first();

        if ($payment) {
            $this->planName   = $payment->plan_name ?? 'Standard';
            $this->planStatus = $payment->status;
            $this->planStart  = $payment->created_at->format('d/m/Y');
            $this->planEnd    = $payment->created_at->copy()->addMonth()->format('d/m/Y');

            $expiry         = $payment->created_at->copy()->addDays(30);
            $diff           = (int) now()->diffInDays($expiry, false);
            $this->daysLeft = $diff > 0 ? $diff : 0;

            // Limites du plan
            $planLimit = PlanLimit::where('plan_name', $payment->plan_name)->first();
            if ($planLimit) {
                $this->limitLives     = $planLimit->max_lives_par_mois;
                $this->limitCommandes = $planLimit->max_commandes_par_mois;
            }
        }

        // Compteurs du mois en cours
        $counter = UsageCounter::where('user_id', $user->id)
            ->where('mois', now()->format('Y-m'))
            ->first();

        if ($counter) {
            $this->totalLives     = $counter->nb_lives_utilises       ?? 0;
            $this->totalCommandes = $counter->nb_commandes_utilises    ?? 0;
            $this->totalExports   = $counter->nb_exports_utilises      ?? 0;
            $this->totalArticles  = $counter->nb_commentaires_utilises ?? 0;
        }

        // Plans
        $this->plans = PlanLimit::all();
    }

    // ─── Changer d'onglet ────────────────────────────────
    public function setTab(string $tab): void
    {
        $this->activeTab      = $tab;
        $this->successMessage = null;
        $this->errorMessage   = null;
    }

    // ─── Mettre à jour le profil ─────────────────────────
    public function updateProfil(): void
    {
        $this->validate([
            'name'  => 'required|min:2|max:100',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ], [
            'name.required'  => 'Le nom est obligatoire.',
            'name.min'       => 'Le nom doit contenir au moins 2 caractères.',
            'email.required' => "L'email est obligatoire.",
            'email.email'    => "L'email n'est pas valide.",
            'email.unique'   => 'Cet email est déjà utilisé.',
        ]);

        User::find(Auth::id())->update([
            'name'  => $this->name,
            'email' => $this->email,
        ]);

        $this->successMessage = 'Profil mis à jour avec succès !';
        $this->errorMessage   = null;
    }

    // ─── Changer le mot de passe ─────────────────────────
    public function updatePassword(): void
    {
        $this->validate([
            'new_password'             => 'required|min:6|same:new_password_confirmation',
            'new_password_confirmation' => 'required',
        ], [
            'new_password.required'             => 'Le nouveau mot de passe est obligatoire.',
            'new_password.min'                  => 'Le mot de passe doit contenir au moins 6 caractères.',
            'new_password.same'                 => 'Les mots de passe ne correspondent pas.',
            'new_password_confirmation.required' => 'Veuillez confirmer le mot de passe.',
        ]);

        User::find(Auth::id())->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->new_password             = '';
        $this->new_password_confirmation = '';
        $this->successMessage           = 'Mot de passe changé avec succès !';
        $this->errorMessage             = null;
    }

    // ─── Ouvrir modal détails plan ───────────────────────
    public function openPlanDetailsModal(int $planId): void
    {
        $plan = PlanLimit::findOrFail($planId);

        $this->selectedPlanId         = $planId;
        $this->showPlanDetailsModal   = true;
        $this->editId                 = $plan->id;
        $this->editPlanName           = $plan->plan_name;
        $this->editMaxLives           = (string) ($plan->max_lives_par_mois    ?? '');
        $this->editMaxCommandes       = (string) ($plan->max_commandes_par_mois ?? '');
        $this->editMaxComptesTiktok   = (string) ($plan->max_comptes_tiktok     ?? '');
        $this->editSupportPrioritaire = (bool)   ($plan->support_prioritaire    ?? false);
        $this->editApiPersonnalisee   = (bool)   ($plan->api_personnalisee      ?? false);
        $this->editManagerDeCompte    = (bool)   ($plan->manager_de_compte      ?? false);
    }

    public function closePlanDetailsModal(): void
    {
        $this->showPlanDetailsModal = false;
        $this->selectedPlanId       = null;
    }

    // ─── Ouvrir modal édition plan ───────────────────────
    public function openEditModal(int $planId): void
    {
        $plan = PlanLimit::findOrFail($planId);

        $this->editId                 = $plan->id;
        $this->editPlanName           = $plan->plan_name;
        $this->editPrix = $plan->prix;
        $this->editMaxLives           = (string) ($plan->max_lives_par_mois    ?? '');
        $this->editMaxCommandes       = (string) ($plan->max_commandes_par_mois ?? '');
        $this->editMaxComptesTiktok   = (string) ($plan->max_comptes_tiktok     ?? '');
        $this->editSupportPrioritaire = (bool)   ($plan->support_prioritaire    ?? false);
        $this->editApiPersonnalisee   = (bool)   ($plan->api_personnalisee      ?? false);
        $this->editManagerDeCompte    = (bool)   ($plan->manager_de_compte      ?? false);

        $this->showEditModal        = true;
        $this->showPlanDetailsModal = false;
        $this->successMessage       = null;
        $this->errorMessage         = null;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->editId        = null;
    }

    // ─── Sauvegarder le plan ─────────────────────────────
    public function savePlan(): void
    {
        if (!$this->editId) {
            $this->errorMessage   = 'Aucun plan sélectionné.';
            $this->successMessage = null;
            return;
        }

        $this->validate([
            'editPlanName'         => 'required|string|max:100',
            'editMaxLives'         => 'nullable|integer|min:0',
            'editMaxCommandes'     => 'nullable|integer|min:0',
            'editMaxComptesTiktok' => 'nullable|integer|min:0',
        ], [
            'editPlanName.required' => 'Le nom du plan est obligatoire.',
        ]);

        PlanLimit::findOrFail($this->editId)->update([
            'plan_name'              => $this->editPlanName,
             'prix'                   => $this->editPrix,   
            'max_lives_par_mois'     => $this->editMaxLives      ?: null,
            'max_commandes_par_mois' => $this->editMaxCommandes   ?: null,
            'max_comptes_tiktok'     => $this->editMaxComptesTiktok ?: null,
            'support_prioritaire'    => $this->editSupportPrioritaire,
            'api_personnalisee'      => $this->editApiPersonnalisee,
            'manager_de_compte'      => $this->editManagerDeCompte,
        ]);

        $this->plans                = PlanLimit::all();
        $this->showPlanDetailsModal = false;
        $this->showEditModal        = false;
        $this->successMessage       = '✅ Plan mis à jour avec succès !';
        $this->errorMessage         = null;
    }

    public function render()
    {
        return view('livewire.admin.settings', [
            'plans' => PlanLimit::all(),
        ]);
    }
}