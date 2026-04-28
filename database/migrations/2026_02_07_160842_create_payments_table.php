<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Le client qui paie
        $table->string('plan_name'); // Starter, Business, ou Enterprise
        $table->decimal('amount', 8, 2); // Le montant (ex: 79.00)
        $table->enum('status', ['en_attente', 'accepte', 'refuse'])->default('en_attente');
 // État du paiement
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
