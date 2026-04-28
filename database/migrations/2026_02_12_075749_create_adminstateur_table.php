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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom de l'admin
            $table->string('email')->unique(); // Email unique pour la connexion
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // Mot de passe haché
            $table->rememberToken(); // Pour la fonction "se souvenir de moi"
            $table->timestamps(); // created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adminstateur');
    }
};
