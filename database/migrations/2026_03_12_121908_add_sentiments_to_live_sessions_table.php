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
        Schema::table('live_sessions', function (Blueprint $table) {
        $table->integer('sentiment_positive')->default(0)->after('total_phones');
        $table->integer('sentiment_negative')->default(0)->after('sentiment_positive');
        $table->integer('sentiment_neutral')->default(0)->after('sentiment_negative');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('live_sessions', function (Blueprint $table) {
        $table->dropColumn(['sentiment_positive', 'sentiment_negative', 'sentiment_neutral']);
    });
    }
};
