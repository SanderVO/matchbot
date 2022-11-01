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
        Schema::create('team_results', function (Blueprint $table) {
            $table->id();
            $table->integer('score')->nullable();
            $table->integer('crawl_score')->nullable();
            $table->string('comment')->nullable();
            $table->unsignedBigInteger('team_id');
            $table->unsignedBigInteger('event_id');
            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign('team_id')
                ->references('id')
                ->on('teams');

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
        Schema::dropIfExists('team_results');
    }
};
