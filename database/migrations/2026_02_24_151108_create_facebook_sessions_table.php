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
        Schema::create('facebook_sessions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id');
    $table->string('fb_username')->nullable();
    $table->string('live_link')->nullable();
    $table->integer('total_comments')->default(0);
    $table->integer('total_clients')->default(0);
    $table->integer('total_articles')->default(0);
    $table->integer('total_phones')->default(0);
    $table->text('raw_data')->nullable(); // Contenu complet du live
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_sessions');
    }
};
