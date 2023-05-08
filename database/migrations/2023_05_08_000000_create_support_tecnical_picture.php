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
        Schema::create('support_tecnical_picture', function (Blueprint $table) {
            $table->unsignedBigInteger('id_support_technical')->nullable();
            $table->foreign('id_support_technical')->references('id')->on('support_technical')->onDelete('cascade');
            $table->unsignedBigInteger('id_picture')->nullable();
            $table->foreign('id_picture')->references('id')->on('picture')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('support_tecnical_picture', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_support_technical');
            $table->dropConstrainedForeignId('id_picture');
        });
    }
};
