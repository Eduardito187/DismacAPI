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
        Schema::table('filter', function (Blueprint $table) {
            $table->unsignedBigInteger('id_attribute');
            $table->foreign('id_attribute')->references('id')->on('attributes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('filter', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_attribute');
        });
    }
};
