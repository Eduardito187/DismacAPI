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
        Schema::create('process_task', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_process')->nullable();
            $table->foreign('id_process')->references('id')->on('process')->onDelete('cascade');
            $table->unsignedBigInteger('id_partner')->nullable();
            $table->foreign('id_partner')->references('id')->on('partner')->onDelete('cascade');
            $table->string('mensaje')->nullable();
            $table->string('duracion')->nullable();
            $table->boolean('status');
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
        Schema::dropIfExists('process_task', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_process');
            $table->dropConstrainedForeignId('id_partner');
        });
    }
};
