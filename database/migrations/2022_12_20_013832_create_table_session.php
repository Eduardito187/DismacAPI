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
        Schema::create('session', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unsignedBigInteger('id_ip')->nullable();
            $table->unsignedBigInteger('id_localization')->nullable();
            $table->foreign('id_ip')->references('id')->on('ip')->onDelete('cascade');
            $table->foreign('id_localization')->references('id')->on('localization')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('session', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_ip');
            $table->dropConstrainedForeignId('id_localization');
        });
    }
};
