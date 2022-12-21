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
        Schema::create('filter_display', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('status');
            $table->boolean('navigation');
            $table->integer('position');
            $table->unsignedBigInteger('id_display_info');
            $table->foreign('id_display_info')->references('id')->on('filter_display_info')->onDelete('cascade');
            $table->unsignedBigInteger('id_info_filter');
            $table->foreign('id_info_filter')->references('id')->on('filter_info')->onDelete('cascade');
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
        Schema::dropIfExists('filter_display', function (Blueprint $table) {
            $table->dropForeign('id_display_info');
            $table->dropIndex('id_display_info');
            $table->dropColumn('id_display_info');
            $table->dropForeign('id_info_filter');
            $table->dropIndex('id_info_filter');
            $table->dropColumn('id_info_filter');
        });
    }
};
