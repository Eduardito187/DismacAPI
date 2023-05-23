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
            $table->bigIncrements('id');
            $table->string('nombre')->nullable();
            $table->string('apellido_paterno')->nullable();
            $table->string('apellido_materno')->nullable();
            $table->string('email')->nullable();
            $table->string('num_telefono')->nullable();
            $table->unsignedBigInteger('tipo_documento')->nullable();
            $table->foreign('tipo_documento')->references('id')->on('tipo_documento')->onDelete('cascade');
            $table->string('num_documento')->nullable();
            $table->unsignedBigInteger('country')->nullable();
            $table->foreign('country')->references('id')->on('country')->onDelete('cascade');
            $table->unsignedBigInteger('city')->nullable();
            $table->foreign('city')->references('id')->on('city')->onDelete('cascade');
            $table->unsignedBigInteger('municipality')->nullable();
            $table->foreign('municipality')->references('id')->on('municipality')->onDelete('cascade');
            $table->string('direccion')->nullable();
            $table->string('direccion_extra')->nullable();
            $table->unsignedBigInteger('localization')->nullable();
            $table->foreign('localization')->references('id')->on('localization')->onDelete('cascade');
            $table->timestamp('fecha_entrega');
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
        Schema::dropIfExists('shipping_address', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tipo_documento');
            $table->dropConstrainedForeignId('country');
            $table->dropConstrainedForeignId('city');
            $table->dropConstrainedForeignId('municipality');
            $table->dropConstrainedForeignId('localization');
        });
    }
};
