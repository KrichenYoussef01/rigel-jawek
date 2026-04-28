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
        Schema::create('user_live_sessions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->enum('platform', ['tiktok', 'facebook']);
    $table->string('link')->nullable();
    $table->string('username')->nullable(); // TikTok username ou Facebook username
    $table->integer('total_comments')->default(0);
    $table->integer('total_clients')->default(0);
    $table->integer('total_articles')->default(0);
    $table->integer('total_phones')->default(0);
    $table->timestamp('started_at')->nullable();
    $table->timestamp('ended_at')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_live_sessions');
    }
};
