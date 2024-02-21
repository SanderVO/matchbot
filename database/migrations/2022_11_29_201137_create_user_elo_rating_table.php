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
        Schema::create('user_elo_ratings', function (Blueprint $table) {
            $table->id();
            $table->integer('elo_rating');
            $table->string('objectable_type')->nullable();
            $table->unsignedBigInteger('objectable_id')->nullable();
            $table->string('scorable_type');
            $table->unsignedBigInteger('scorable_id');
            $table->unsignedBigInteger('event_id');
            $table->timestamps();

            $table
                ->foreign('event_id')
                ->references('id')
                ->on('events');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_elo_ratings');
    }
};
