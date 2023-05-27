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
        Schema::create('coupon', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100)->nullable();
            $table->longText('description');
            $table->string('coupon_code', 50)->nullable();
            $table->unsignedBigInteger('id_partner')->nullable();
            $table->foreign('id_partner')->references('id')->on('partner')->onDelete('cascade');
            $table->unsignedBigInteger('type_discount')->nullable();
            $table->foreign('type_discount')->references('id')->on('tipo_coupon')->onDelete('cascade');
            $table->integer('limit_client');
            $table->integer('limit_usage');
            $table->boolean('status');
            $table->double('percent', 10, 2)->nullable();
            $table->timestamp('from_date')->nullable();
            $table->timestamp('to_date')->nullable();
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
        Schema::dropIfExists('coupon', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_partner');
            $table->dropConstrainedForeignId('type_discount');
        });
    }
};
