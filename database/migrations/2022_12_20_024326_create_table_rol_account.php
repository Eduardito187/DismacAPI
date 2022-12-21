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
        Schema::create('rol_account', function (Blueprint $table) {
            $table->unsignedBigInteger('id_rol');
            $table->unsignedBigInteger('id_account');
            $table->foreign('id_rol')->references('id')->on('rol')->onDelete('cascade');
            $table->foreign('id_account')->references('id')->on('account')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rol_account', function (Blueprint $table) {
            $table->dropForeign('id_rol');
            $table->dropIndex('id_rol');
            $table->dropColumn('id_rol');
            $table->dropForeign('id_account');
            $table->dropIndex('id_account');
            $table->dropColumn('id_account');
        });
    }
};
