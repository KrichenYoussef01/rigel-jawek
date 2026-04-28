<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plan_limits', function (Blueprint $table) {
            $table->id();
            $table->string('plan_name', 50)->unique(); // 'Starter', 'Business', 'Enterprise'

            // ── Lives ──
            $table->unsignedInteger('max_lives_par_mois')->nullable();        // NULL = illimité

            // ── Commandes & Commentaires ──
            $table->unsignedInteger('max_commandes_par_mois')->nullable();
            $table->unsignedInteger('max_commentaires_par_live')->nullable();

            // ── Catalogue ──
            $table->unsignedInteger('max_produits')->nullable();

            // ── Utilisateurs & Comptes ──
            $table->unsignedInteger('max_utilisateurs')->nullable();
            $table->unsignedInteger('max_comptes_tiktok')->default(1);

            // ── Exports ──
            $table->unsignedInteger('max_exports_par_jour')->nullable();       // NULL = illimité

            // ── Fonctionnalités booléennes ──
            $table->boolean('support_prioritaire')->default(false);
            $table->boolean('api_personnalisee')->default(false);
            $table->boolean('multi_comptes_tiktok')->default(false);
            $table->boolean('manager_de_compte')->default(false);
            $table->boolean('extraction_temps_reel')->default(false);
            $table->boolean('paniers_automatiques')->default(false);

            $table->timestamps();
        });
        DB::table('plan_limits')->insert([
            [
                'plan_name'                => 'Starter',
                'max_lives_par_mois'       => 5,
                'max_commandes_par_mois'   => 500,
                'max_commentaires_par_live'=> 1000,
                'max_produits'             => 50,
                'max_utilisateurs'         => 1,
                'max_comptes_tiktok'       => 1,
                'max_exports_par_jour'     => 1,
                'support_prioritaire'      => false,
                'api_personnalisee'        => false,
                'multi_comptes_tiktok'     => false,
                'manager_de_compte'        => false,
                'extraction_temps_reel'    => false,
                'paniers_automatiques'     => false,
                'created_at'               => now(),
                'updated_at'               => now(),
            ],
            [
                'plan_name'                => 'Business',
                'max_lives_par_mois'       => null,    // illimité
                'max_commandes_par_mois'   => 5000,
                'max_commentaires_par_live'=> 10000,
                'max_produits'             => 500,
                'max_utilisateurs'         => 3,
                'max_comptes_tiktok'       => 1,
                'max_exports_par_jour'     => null,    // illimité
                'support_prioritaire'      => true,
                'api_personnalisee'        => false,
                'multi_comptes_tiktok'     => false,
                'manager_de_compte'        => false,
                'extraction_temps_reel'    => true,
                'paniers_automatiques'     => true,
                'created_at'               => now(),
                'updated_at'               => now(),
            ],
            [
                'plan_name'                => 'Enterprise',
                'max_lives_par_mois'       => null,    // illimité
                'max_commandes_par_mois'   => null,    // illimité
                'max_commentaires_par_live'=> null,    // illimité
                'max_produits'             => null,    // illimité
                'max_utilisateurs'         => null,    // illimité
                'max_comptes_tiktok'       => 10,
                'max_exports_par_jour'     => null,    // illimité
                'support_prioritaire'      => true,
                'api_personnalisee'        => true,
                'multi_comptes_tiktok'     => true,
                'manager_de_compte'        => true,
                'extraction_temps_reel'    => true,
                'paniers_automatiques'     => true,
                'created_at'               => now(),
                'updated_at'               => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_limits');
    }
};
