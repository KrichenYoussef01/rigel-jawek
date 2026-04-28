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
    Schema::table('codes', function (Blueprint $table) {
        // Supprimer l'ancienne contrainte unique sur code seul
        $table->dropUnique(['code']);

        // Ajouter une contrainte unique sur (user_id + code)
        $table->unique(['user_id', 'code']);
    });
}

public function down()
{
    Schema::table('codes', function (Blueprint $table) {
        $table->dropUnique(['user_id', 'code']);
        $table->unique('code');
    });
}
};
