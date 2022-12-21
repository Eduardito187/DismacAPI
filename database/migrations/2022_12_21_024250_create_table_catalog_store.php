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
        Schema::create('catalog_store', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_catalog');
            $table->foreign('id_catalog')->references('id')->on('catalog')->onDelete('cascade');
            $table->unsignedBigInteger('id_store');
            $table->foreign('id_store')->references('id')->on('store')->onDelete('cascade');
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
        Schema::dropIfExists('catalog_store', function (Blueprint $table) {
            $table->dropForeign('id_catalog');
            $table->dropIndex('id_catalog');
            $table->dropColumn('id_catalog');
            $table->dropForeign('id_store');
            $table->dropIndex('id_store');
            $table->dropColumn('id_store');
            $table->dropForeign('id_account');
            $table->dropIndex('id_account');
            $table->dropColumn('id_account');
        });
    }
};
