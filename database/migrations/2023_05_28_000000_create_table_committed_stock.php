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
        Schema::create('committed_stock', function (Blueprint $table) {
            $table->unsignedBigInteger('sales')->nullable();
            $table->foreign('sales')->references('id')->on('sales')->onDelete('cascade');
            $table->unsignedBigInteger('product')->nullable();
            $table->foreign('product')->references('id')->on('product')->onDelete('cascade');
            $table->unsignedBigInteger('warehouse')->nullable();
            $table->foreign('warehouse')->references('id')->on('warehouses')->onDelete('cascade');
            $table->integer('qty');
            $table->boolean('status');
            $table->timestamp('date_limit')->nullable();
            $table->timestamp('created_at')->nullable();
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
        Schema::dropIfExists('committed_stock', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product');
            $table->dropConstrainedForeignId('sales');
            $table->dropConstrainedForeignId('warehouse');
        });
    }
};
