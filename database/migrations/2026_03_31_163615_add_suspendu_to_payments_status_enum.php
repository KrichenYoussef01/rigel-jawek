<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::statement("ALTER TABLE payments MODIFY status ENUM('en_attente', 'accepte', 'refuse', 'suspendu') NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE payments MODIFY status ENUM('en_attente', 'accepte', 'refuse') NOT NULL");
    }
};
