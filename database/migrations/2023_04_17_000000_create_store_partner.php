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
        Schema::create('store_partner', function (Blueprint $table) {
            $table->unsignedBigInteger('id_store')->nullable();
            $table->foreign('id_store')->references('id')->on('store')->onDelete('cascade');
            $table->unsignedBigInteger('id_partner')->nullable();
            $table->foreign('id_partner')->references('id')->on('partner')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store_partner', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_store');
            $table->dropConstrainedForeignId('id_partner');
        });
    }
};
