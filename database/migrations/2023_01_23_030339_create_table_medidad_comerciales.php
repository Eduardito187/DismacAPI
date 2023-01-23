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
        Schema::create('medidas_comerciales', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->string('longitud')->nullable();
            $table->string('ancho')->nullable();
            $table->string('altura')->nullable();
            $table->string('volumen')->nullable();
            $table->string('peso')->nullable();
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
        Schema::dropIfExists('medidas_comerciales');
    }
};
