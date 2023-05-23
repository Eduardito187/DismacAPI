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
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->integer('products');
            $table->unsignedBigInteger('account')->nullable();
            $table->foreign('account')->references('id')->on('account')->onDelete('cascade');
            $table->unsignedBigInteger('status')->nullable();
            $table->foreign('status')->references('id')->on('status_order')->onDelete('cascade');
            $table->unsignedBigInteger('shipping_address')->nullable();
            $table->foreign('shipping_address')->references('id')->on('shipping_address')->onDelete('cascade');
            $table->double('discount', 10, 2)->nullable();
            $table->double('subtotal', 10, 2)->nullable();
            $table->double('total', 10, 2)->nullable();
            $table->string('nro_factura')->nullable();
            $table->string('nro_proforma')->nullable();
            $table->string('nro_control')->nullable();
            $table->string('ip_client', 20)->nullable();
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
        Schema::dropIfExists('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('account');
            $table->dropConstrainedForeignId('status');
            $table->dropConstrainedForeignId('shipping_address');
        });
    }
};
