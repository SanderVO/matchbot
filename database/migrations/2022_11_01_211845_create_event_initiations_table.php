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
        Schema::create('event_initiations', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('auto_start');
            $table->string('authorization_user_id')->nullable();
            $table->string('authorization_message_id');
            $table->dateTime('expire_at');
            $table->unsignedBigInteger('event_id');
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('event_initiations');
    }
};
