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
        Schema::create('shipping_address', function (Blueprint $table) {
            $table->unsignedBigInteger('customer')->nullable();
            $table->foreign('customer')->references('id')->on('customers_dis')->onDelete('cascade');
            $table->unsignedBigInteger('address')->nullable();
            $table->foreign('address')->references('id')->on('address')->onDelete('cascade');
            $table->unsignedBigInteger('sale')->nullable();
            $table->foreign('sale')->references('id')->on('sales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shipping_address', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer');
            $table->dropConstrainedForeignId('address');
            $table->dropConstrainedForeignId('sale');
        });
    }
};
