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
            $table->unsignedBigInteger('id_municipality');
            $table->unsignedBigInteger('id_country');
            $table->unsignedBigInteger('id_city');
            $table->unsignedBigInteger('id_address_extra');
            $table->unsignedBigInteger('id_localization');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');
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
            $table->dropForeign('id_municipality');
            $table->dropIndex('id_municipality');
            $table->dropColumn('id_municipality');
            $table->dropForeign('id_country');
            $table->dropIndex('id_country');
            $table->dropColumn('id_country');
            $table->dropForeign('id_city');
            $table->dropIndex('id_city');
            $table->dropColumn('id_city');
            $table->dropForeign('id_address_extra');
            $table->dropIndex('id_address_extra');
            $table->dropColumn('id_address_extra');
            $table->dropForeign('id_localization');
            $table->dropIndex('id_localization');
            $table->dropColumn('id_localization');
        });
    }
};
