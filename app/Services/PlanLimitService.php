<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PlanLimit;
use App\Models\UsageCounter;
use Illuminate\Support\Carbon;

class PlanLimitService
{
    /**
     * Récupère le plan actif d'un utilisateur.
     */
    public function getPlanActif(int $userId): ?Payment
    {
        return Payment::where('user_id', $userId)
            ->where('status', 'accepte')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    /**
     * Récupère les limites du plan actif.
     */
    public function getLimites(int $userId): ?PlanLimit
    {
        $payment = $this->getPlanActif($userId);
        if (!$payment) return null;

        return PlanLimit::where('plan_name', $payment->plan_name)->first();
    }

    /**
     * Récupère (ou crée) le compteur du mois en cours.
     */
    public function getCompteur(int $userId): UsageCounter
    {
        return UsageCounter::firstOrCreate([
            'user_id' => $userId,
            'mois'    => Carbon::now()->format('Y-m'),
        ]);
    }
    public function peutAjouterCommande(int $userId): bool
{
    $limites  = $this->getLimites($userId);
    $compteur = $this->getCompteur($userId);

    if (!$limites) return false;
    
    // Si max_commandes_par_mois est null, c'est illimité
    if ($limites->max_commandes_par_mois === null) return true;

    return $compteur->nb_commandes_utilises < $limites->max_commandes_par_mois;
}
// Dans IncrementController.php

/**
 * Tente d'incrémenter une commande. 
 * Retourne true si réussi, false si limite atteinte.
 */
public function consommerCommande(int $userId): bool
{
    if (!$this->peutAjouterCommande($userId)) {
        return false;
    }

    $this->incrementerCommandes($userId);
    return true;
}

    // ────────────────────────────────────────────
    // VÉRIFICATIONS
    // ────────────────────────────────────────────

    /**
     * Vérifie si l'utilisateur peut lancer un live.
     * Retourne true si autorisé, false sinon.
     */
    public function peutLancerLive(int $userId): bool
    {
        $limites  = $this->getLimites($userId);
        $compteur = $this->getCompteur($userId);

        if (!$limites) return false; // pas d'abonnement actif

        // NULL = illimité
        if ($limites->max_lives_par_mois === null) return true;

        return $compteur->nb_lives_utilises < $limites->max_lives_par_mois;
    }

    /**
     * Vérifie si l'utilisateur peut exporter.
     */
    public function peutExporter(int $userId): bool
    {
        $limites  = $this->getLimites($userId);
        $compteur = $this->getCompteur($userId);

        if (!$limites) return false;
        if ($limites->max_exports_par_jour === null) return true;

        // Comptage des exports du jour
        $exportsAujourdhui = UsageCounter::where('user_id', $userId)
            ->whereDate('updated_at', today())
            ->value('nb_exports_utilises') ?? 0;

        return $exportsAujourdhui < $limites->max_exports_par_jour;
    }

    /**
     * Vérifie si l'utilisateur peut ajouter un produit.
     */
    public function peutAjouterProduit(int $userId, int $nbProduitsActuels): bool
    {
        $limites = $this->getLimites($userId);
        if (!$limites) return false;
        if ($limites->max_produits === null) return true;

        return $nbProduitsActuels < $limites->max_produits;
    }

    // ────────────────────────────────────────────
    // INCRÉMENTS
    // ────────────────────────────────────────────

    public function incrementerLive(int $userId): void
    {
        $this->getCompteur($userId)->increment('nb_lives_utilises');
    }

    public function incrementerCommandes(int $userId, int $nb = 1): void
    {
        $this->getCompteur($userId)->increment('nb_commandes_utilises', $nb);
    }

    public function incrementerCommentaires(int $userId, int $nb = 1): void
    {
        $this->getCompteur($userId)->increment('nb_commentaires_utilises', $nb);
    }

    public function incrementerExport(int $userId): void
    {
        $this->getCompteur($userId)->increment('nb_exports_utilises');
    }

    // ────────────────────────────────────────────
    // RÉSUMÉ
    // ────────────────────────────────────────────

    /**
     * Retourne un résumé de l'utilisation et des limites pour l'affichage.
     */
    public function getResume(int $userId): array
    {
        $limites  = $this->getLimites($userId);
        $compteur = $this->getCompteur($userId);
        $payment  = $this->getPlanActif($userId);

        if (!$limites || !$payment) {
            return ['erreur' => 'Aucun abonnement actif'];
        }

        return [
            'plan'       => $payment->plan_name,
            'expire_le'  => $payment->expires_at,
            'lives'      => [
                'utilises' => $compteur->nb_lives_utilises,
                'limite'   => $limites->max_lives_par_mois ?? '∞',
            ],
            'commandes'  => [
                'utilises' => $compteur->nb_commandes_utilises,
                'limite'   => $limites->max_commandes_par_mois ?? '∞',
            ],
            'exports'    => [
                'utilises' => $compteur->nb_exports_utilises,
                'limite'   => $limites->max_exports_par_jour ?? '∞',
            ],
            'fonctionnalites' => [
                'support_prioritaire'   => $limites->support_prioritaire,
                'api_personnalisee'     => $limites->api_personnalisee,
                'multi_comptes_tiktok'  => $limites->multi_comptes_tiktok,
                'extraction_temps_reel' => $limites->extraction_temps_reel,
                'paniers_automatiques'  => $limites->paniers_automatiques,
            ],
        ];
    }
}