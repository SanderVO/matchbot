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
        Schema::create('event_initiation_messages', function (Blueprint $table) {
            $table->id();
            $table->string('authorization_message_id');
            $table->string('authorization_channel_id');
            $table->unsignedBigInteger('event_initiation_id');
            $table->timestamps();
            $table->softDeletes();

            $table
                ->foreign('event_initiation_id')
                ->references('id')
                ->on('event_initiations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_initiation_messages');
    }
};
