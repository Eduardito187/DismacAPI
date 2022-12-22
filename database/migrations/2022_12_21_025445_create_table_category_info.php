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
        Schema::create('category_info', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('show_filter');
            $table->integer('id_pos');
            $table->boolean('sub_category_pos');
            $table->unsignedBigInteger('id_picture');
            $table->foreign('id_picture')->references('id')->on('picture')->onDelete('cascade');
            $table->unsignedBigInteger('id_content');
            $table->foreign('id_content')->references('id')->on('content')->onDelete('cascade');
            $table->longText('url');
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
        Schema::dropIfExists('category_info', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_picture');
            $table->dropConstrainedForeignId('id_content');
        });
    }
};
