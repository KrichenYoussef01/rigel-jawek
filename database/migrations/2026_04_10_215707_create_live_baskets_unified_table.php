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
        Schema::create('live_baskets_unified', function (Blueprint $table) {
    $table->id();
    $table->foreignId('session_id')->constrained('user_live_sessions')->onDelete('cascade');
    $table->string('client_name');
    $table->json('articles');
    $table->json('phones');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_baskets_unified');
    }
};
