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
        Schema::create('partner_address', function (Blueprint $table) {
            $table->unsignedBigInteger('id_partner');
            $table->unsignedBigInteger('id_address');
            $table->foreign('id_partner')->references('id')->on('partner')->onDelete('cascade');
            $table->foreign('id_address')->references('id')->on('address')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_address', function (Blueprint $table) {
            $table->dropForeign('id_partner');
            $table->dropIndex('id_partner');
            $table->dropColumn('id_partner');
            $table->dropForeign('id_address');
            $table->dropIndex('id_address');
            $table->dropColumn('id_address');
        });
    }
};
