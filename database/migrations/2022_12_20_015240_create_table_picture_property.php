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
        Schema::create('picture_property', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('format', 10);
            $table->unsignedBigInteger('id_picture');
            $table->unsignedBigInteger('id_dimensions');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->foreign('id_picture')->references('id')->on('picture')->onDelete('cascade');
            $table->foreign('id_dimensions')->references('id')->on('dimensions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('picture_property', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_picture');
            $table->dropConstrainedForeignId('id_dimensions');
        });
    }
};
