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
        Schema::create('address', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_municipality')->nullable();
            $table->unsignedBigInteger('id_country')->nullable();
            $table->unsignedBigInteger('id_city')->nullable();
            $table->unsignedBigInteger('id_address_extra')->nullable();
            $table->unsignedBigInteger('id_localization')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->foreign('id_municipality')->references('id')->on('municipality')->onDelete('cascade');
            $table->foreign('id_country')->references('id')->on('country')->onDelete('cascade');
            $table->foreign('id_city')->references('id')->on('city')->onDelete('cascade');
            $table->foreign('id_address_extra')->references('id')->on('address_extra')->onDelete('cascade');
            $table->foreign('id_localization')->references('id')->on('localization')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('address', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_municipality');
            $table->dropConstrainedForeignId('id_country');
            $table->dropConstrainedForeignId('id_city');
            $table->dropConstrainedForeignId('id_address_extra');
            $table->dropConstrainedForeignId('id_localization');
        });
    }
};
