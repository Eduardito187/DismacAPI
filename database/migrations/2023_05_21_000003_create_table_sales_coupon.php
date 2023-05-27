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
        Schema::create('sales_coupon', function (Blueprint $table) {
            $table->unsignedBigInteger('sales')->nullable();
            $table->foreign('sales')->references('id')->on('sales')->onDelete('cascade');
            $table->unsignedBigInteger('coupon')->nullable();
            $table->foreign('coupon')->references('id')->on('coupon')->onDelete('cascade');
            $table->double('monto', 10, 2)->nullable();
            $table->double('percent', 10, 2)->nullable();
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
        Schema::dropIfExists('sales_coupon', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon');
            $table->dropConstrainedForeignId('sales');
        });
    }
};
