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
        Schema::create('partner_session', function (Blueprint $table) {
            $table->unsignedBigInteger('id_partner');
            $table->unsignedBigInteger('id_session');
            $table->boolean('status');
            $table->foreign('id_partner')->references('id')->on('partner')->onDelete('cascade');
            $table->foreign('id_session')->references('id')->on('session')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_session', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_partner');
            $table->dropConstrainedForeignId('id_session');
        });
    }
};
