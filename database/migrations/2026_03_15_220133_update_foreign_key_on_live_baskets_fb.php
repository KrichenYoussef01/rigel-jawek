<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Supprimer l'ancienne clé étrangère
        Schema::table('live_baskets_fb', function (Blueprint $table) {
            $table->dropForeign(['live_session_id']);
        });

        // Ajouter la nouvelle clé étrangère pointant vers facebook_sessions
        Schema::table('live_baskets_fb', function (Blueprint $table) {
            $table->foreign('live_session_id')
                  ->references('id')
                  ->on('facebook_sessions')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Restaurer l'ancienne contrainte (au cas où)
        Schema::table('live_baskets_fb', function (Blueprint $table) {
            $table->dropForeign(['live_session_id']);
            $table->foreign('live_session_id')
                  ->references('id')
                  ->on('live_sessions')
                  ->onDelete('cascade');
        });
    }
};