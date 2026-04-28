<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('live_baskets_fb', function (Blueprint $table) {
            $table->renameColumn('live_session_id', 'facebook_session_id');
        });
    }

    public function down(): void
    {
        Schema::table('live_baskets_fb', function (Blueprint $table) {
            $table->renameColumn('facebook_session_id', 'live_session_id');
        });
    }
};