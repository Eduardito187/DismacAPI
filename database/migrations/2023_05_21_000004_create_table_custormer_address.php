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
        Schema::create('customer_address', function (Blueprint $table) {
            $table->unsignedBigInteger('customer')->nullable();
            $table->foreign('customer')->references('id')->on('customers_dis')->onDelete('cascade');
            $table->unsignedBigInteger('address')->nullable();
            $table->foreign('address')->references('id')->on('address')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customer_address', function (Blueprint $table) {
            $table->dropConstrainedForeignId('customer');
            $table->dropConstrainedForeignId('address');
        });
    }
};
