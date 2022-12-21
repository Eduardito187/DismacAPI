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
        Schema::create('clacom', function (Blueprint $table) {
            $table->id();
            $table->string('label', 50);
            $table->string('code', 20);
            $table->unsignedBigInteger('id_picture');
            $table->foreign('id_picture')->references('id')->on('picture')->onDelete('cascade');
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
        Schema::dropIfExists('clacom', function (Blueprint $table) {
            $table->dropForeign('id_picture');
            $table->dropIndex('id_picture');
            $table->dropColumn('id_picture');
        });
    }
};
