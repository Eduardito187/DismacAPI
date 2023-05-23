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
        Schema::create('sales_details', function (Blueprint $table) {
            $table->unsignedBigInteger('sales')->nullable();
            $table->foreign('sales')->references('id')->on('sales')->onDelete('cascade');
            $table->unsignedBigInteger('product')->nullable();
            $table->foreign('product')->references('id')->on('product')->onDelete('cascade');
            $table->integer('qty');
            $table->double('subtotal', 10, 2)->nullable();
            $table->double('total', 10, 2)->nullable();
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
        Schema::dropIfExists('sales_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product');
            $table->dropConstrainedForeignId('sales');
        });
    }
};
