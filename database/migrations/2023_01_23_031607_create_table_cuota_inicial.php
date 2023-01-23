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
        Schema::create('cuota_inicial', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->decimal('inicial', 10,2);
            $table->unsignedBigInteger('id_store')->nullable();
            $table->foreign('id_store')->references('id')->on('store')->onDelete('cascade');
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
        Schema::dropIfExists('cuota_inicial', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_store');
        });
    }
};
