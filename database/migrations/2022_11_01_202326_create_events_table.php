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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->unsignedBigInteger('event_type_id');
            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign('event_type_id')
                ->references('id')
                ->on('event_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
};
