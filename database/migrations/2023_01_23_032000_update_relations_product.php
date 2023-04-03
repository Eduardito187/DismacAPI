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
        Schema::table('product', function (Blueprint $table) {
            $table->unsignedBigInteger('id_description')->nullable();
            $table->foreign('id_description')->references('id')->on('product_description')->onDelete('cascade');
            $table->unsignedBigInteger('id_type')->nullable();
            $table->foreign('id_type')->references('id')->on('product_type')->onDelete('cascade');
            $table->unsignedBigInteger('id_medidas_comerciales')->nullable();
            $table->foreign('id_medidas_comerciales')->references('id')->on('medidas_comerciales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_description');
            $table->dropConstrainedForeignId('id_type');
            $table->dropConstrainedForeignId('id_medidas_comerciales');
        });
    }
};
