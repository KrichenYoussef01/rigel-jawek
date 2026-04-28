<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usage_counters', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->string('mois', 7); // format: '2026-03'

            // ── Compteurs ──
            $table->unsignedInteger('nb_lives_utilises')->default(0);
            $table->unsignedInteger('nb_commandes_utilises')->default(0);
            $table->unsignedInteger('nb_commentaires_utilises')->default(0);
            $table->unsignedInteger('nb_exports_utilises')->default(0);

            $table->timestamps();

            // Un seul enregistrement par user par mois
            $table->unique(['user_id', 'mois']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage_counters');
    }
};
