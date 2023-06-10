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
        Schema::table('committed_stock', function (Blueprint $table) {
            $table->unsignedBigInteger('store')->nullable();
            $table->foreign('store')->references('id')->on('store')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('committed_stock', function (Blueprint $table) {
            $table->dropConstrainedForeignId('store');
        });
    }
};
