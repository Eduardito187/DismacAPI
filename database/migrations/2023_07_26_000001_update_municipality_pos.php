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
        Schema::table('delimitations', function (Blueprint $table) {
            $table->unsignedBigInteger('id_municipality_pos')->nullable();
            $table->foreign('id_municipality_pos')->references('id')->on('municipality_pos')->onDelete('cascade');
        });
        Schema::table('warehouse', function (Blueprint $table) {
            $table->unsignedBigInteger('id_municipality_pos')->nullable();
            $table->foreign('id_municipality_pos')->references('id')->on('municipality_pos')->onDelete('cascade');
            $table->boolean('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delimitations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_municipality_pos');
        });
        Schema::table('warehouse', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_municipality_pos');
            $table->dropColumn('status');
        });
    }
};