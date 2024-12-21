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
        Schema::table('user_elo_ratings', function (Blueprint $table) {
            $table->timestamp('event_start_date')
                ->after('event_id')
                ->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_elo_ratings', function (Blueprint $table) {
            $table->dropColumn('event_start_date');
        });
    }
};