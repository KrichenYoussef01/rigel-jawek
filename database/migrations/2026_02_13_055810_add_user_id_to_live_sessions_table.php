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
        // Si la table n'existe pas, la créer complètement
        if (!Schema::hasTable('live_sessions')) {
            Schema::create('live_sessions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('tiktok_username')->nullable();
                $table->integer('total_comments')->default(0);
                $table->integer('total_clients')->default(0);
                $table->integer('total_articles')->default(0);
                $table->integer('total_phones')->default(0);
                $table->timestamps();
            });
        } else {
            // Sinon, ajouter les colonnes manquantes
            Schema::table('live_sessions', function (Blueprint $table) {
                if (!Schema::hasColumn('live_sessions', 'user_id')) {
                    $table->foreignId('user_id')->after('id')->nullable()->constrained()->onDelete('cascade');
                }
                
                if (!Schema::hasColumn('live_sessions', 'tiktok_username')) {
                    $table->string('tiktok_username')->nullable()->after('user_id');
                }
                
                if (!Schema::hasColumn('live_sessions', 'total_phones')) {
                    $table->integer('total_phones')->default(0)->after('total_articles');
                }
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('live_sessions');
    }
};