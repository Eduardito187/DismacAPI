<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('process', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('File')->nullable();
            $table->foreign('File')->references('id')->on('picture')->onDelete('cascade');
            $table->unsignedBigInteger('Partner')->nullable();
            $table->foreign('Partner')->references('id')->on('partner')->onDelete('cascade');
            $table->string('Type')->nullable();
            $table->string('Ejecucion')->nullable();
            $table->string('Duracion')->nullable();
            $table->timestamp('FechaEjecucion')->nullable();
            $table->timestamp('FechaDuracion')->nullable();
            $table->boolean('Status');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('process', function (Blueprint $table) {
            $table->dropConstrainedForeignId('File');
            $table->dropConstrainedForeignId('Partner');
        });
    }
};
