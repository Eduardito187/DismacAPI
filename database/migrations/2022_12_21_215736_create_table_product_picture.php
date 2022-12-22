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
        Schema::create('product_picture', function (Blueprint $table) {
            $table->unsignedBigInteger('id_product');
            $table->foreign('id_product')->references('id')->on('product')->onDelete('cascade');
            $table->unsignedBigInteger('id_picture');
            $table->foreign('id_picture')->references('id')->on('picture')->onDelete('cascade');
            $table->unsignedBigInteger('id_category');
            $table->foreign('id_category')->references('id')->on('category')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_picture', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_product');
            $table->dropConstrainedForeignId('id_picture');
            $table->dropConstrainedForeignId('id_category');
        });
    }
};
