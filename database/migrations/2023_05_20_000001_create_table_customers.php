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
        Schema::create('customers_dis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre')->nullable();
            $table->string('apellido_paterno')->nullable();
            $table->string('apellido_materno')->nullable();
            $table->string('email')->nullable();
            $table->string('num_telefono')->nullable();
            $table->unsignedBigInteger('tipo_documento')->nullable();
            $table->foreign('tipo_documento')->references('id')->on('tipo_documento')->onDelete('cascade');
            $table->string('num_documento')->nullable();
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
        Schema::dropIfExists('customers_dis', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tipo_documento');
        });
    }
};
