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
        Schema::create('detail_discount', function (Blueprint $table) {
            $table->unsignedBigInteger('sales')->nullable();
            $table->foreign('sales')->references('id')->on('sales')->onDelete('cascade');
            $table->unsignedBigInteger('product')->nullable();
            $table->foreign('product')->references('id')->on('product')->onDelete('cascade');
            $table->unsignedBigInteger('id_coupon')->nullable();
            $table->foreign('id_coupon')->references('id')->on('coupon')->onDelete('cascade');
            $table->double('monto', 10, 2)->nullable();
            $table->double('porcentaje', 10, 2)->nullable();
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
        Schema::dropIfExists('detail_discount', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product');
            $table->dropConstrainedForeignId('sales');
            $table->dropConstrainedForeignId('coupon');
        });
    }
};
