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
        Schema::create('catalog_partner', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_catalog');
            $table->foreign('id_catalog')->references('id')->on('catalog')->onDelete('cascade');
            $table->unsignedBigInteger('id_partner');
            $table->foreign('id_partner')->references('id')->on('partner')->onDelete('cascade');
            $table->unsignedBigInteger('id_account');
            $table->foreign('id_account')->references('id')->on('account')->onDelete('cascade');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalog_partner', function (Blueprint $table) {
            $table->dropForeign('id_catalog');
            $table->dropIndex('id_catalog');
            $table->dropColumn('id_catalog');
            $table->dropForeign('id_partner');
            $table->dropIndex('id_partner');
            $table->dropColumn('id_partner');
            $table->dropForeign('id_account');
            $table->dropIndex('id_account');
            $table->dropColumn('id_account');
        });
    }
};
