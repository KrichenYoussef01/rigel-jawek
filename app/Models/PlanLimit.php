<?php
// ══════════════════════════════════════════════
// app/Models/PlanLimit.php
// ══════════════════════════════════════════════

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanLimit extends Model
{
    protected $fillable = [
        'plan_name',
         'prix',   
        'max_lives_par_mois',
        'max_commandes_par_mois',
        'max_commentaires_par_live',
        'max_produits',
        'max_utilisateurs',
        'max_comptes_tiktok',
        'max_exports_par_jour',
        'support_prioritaire',
        'api_personnalisee',
        'multi_comptes_tiktok',
        'manager_de_compte',
        'extraction_temps_reel',
        'paniers_automatiques',
    ];

    protected $casts = [
        'support_prioritaire'   => 'boolean',
        'api_personnalisee'     => 'boolean',
        'multi_comptes_tiktok'  => 'boolean',
        'manager_de_compte'     => 'boolean',
        'extraction_temps_reel' => 'boolean',
        'paniers_automatiques'  => 'boolean',
    ];
    public function index()
{
    $plans = PlanLimit::orderBy('id')->get();
    return view('billing.pricing', compact('plans'));
}
}