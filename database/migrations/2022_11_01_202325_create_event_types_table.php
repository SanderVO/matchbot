<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('min_players');
            $table->unsignedInteger('max_players')->nullable();
            $table->unsignedInteger('min_teams');
            $table->unsignedInteger('max_teams')->nullable();
            $table->unsignedBigInteger('event_type_sport_id');
            $table->timestamps();

            $table
                ->foreign('event_type_sport_id')
                ->references('id')
                ->on('event_type_sports');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_types');
    }
};
