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
        Schema::create('filter_hide', function (Blueprint $table) {
            $table->unsignedBigInteger('id_category');
            $table->foreign('id_category')->references('id')->on('category')->onDelete('cascade');
            $table->unsignedBigInteger('id_filter');
            $table->foreign('id_filter')->references('id')->on('filter')->onDelete('cascade');
            $table->boolean('status');
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
        Schema::dropIfExists('filter_hide', function (Blueprint $table) {
            $table->dropForeign('id_category');
            $table->dropIndex('id_category');
            $table->dropColumn('id_category');
            $table->dropForeign('id_filter');
            $table->dropIndex('id_filter');
            $table->dropColumn('id_filter');
        });
    }
};
