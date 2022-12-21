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
        Schema::create('session', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('token');
            $table->dateTime('date', $precision = 0);
            $table->unsignedBigInteger('id_ip');
            $table->unsignedBigInteger('id_localization');
            $table->foreign('id_ip')->references('id')->on('ip')->onDelete('cascade');
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
        Schema::dropIfExists('session', function (Blueprint $table) {
            $table->dropForeign('id_ip');
            $table->dropIndex('id_ip');
            $table->dropColumn('id_ip');
            $table->dropForeign('id_localization');
            $table->dropIndex('id_localization');
            $table->dropColumn('id_localization');
        });
    }
};
