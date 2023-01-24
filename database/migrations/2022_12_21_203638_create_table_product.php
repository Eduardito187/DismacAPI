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
        Schema::create('product', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('sku');
            $table->integer('stock');
            $table->unsignedBigInteger('id_brand')->nullable();
            $table->foreign('id_brand')->references('id')->on('brand')->onDelete('cascade');
            $table->unsignedBigInteger('id_clacom')->nullable();
            $table->foreign('id_clacom')->references('id')->on('clacom')->onDelete('cascade');
            $table->unsignedBigInteger('id_metadata')->nullable();
            $table->foreign('id_metadata')->references('id')->on('metadata')->onDelete('cascade');
            $table->unsignedBigInteger('id_mini_cuota')->nullable();
            $table->foreign('id_mini_cuota')->references('id')->on('mini_cuotas')->onDelete('cascade');
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
        Schema::dropIfExists('product', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_brand');
            $table->dropConstrainedForeignId('id_clacom');
            $table->dropConstrainedForeignId('id_metadata');
            $table->dropConstrainedForeignId('id_mini_cuota');
        });
    }
};
