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
        Schema::create('product_sheet', function (Blueprint $table) {
            $table->unsignedBigInteger('id_product');
            $table->foreign('id_product')->references('id')->on('product')->onDelete('cascade');
            $table->unsignedBigInteger('id_sheet');
            $table->foreign('id_sheet')->references('id')->on('data_sheet')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_sheet', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_product');
            $table->dropConstrainedForeignId('id_sheet');
        });
    }
};
