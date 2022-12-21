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
        Schema::table('picture_property', function (Blueprint $table) {
            $table->unsignedBigInteger('id_property');
            $table->foreign('id_property')->references('id')->on('partner')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('picture_property', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_property');
        });
    }
};
