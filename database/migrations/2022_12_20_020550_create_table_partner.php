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
        Schema::create('partner', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email');
            $table->string('token');
            $table->string('nit', 20)->nullable();
            $table->string('razon_social')->nullable();
            $table->boolean('status');
            $table->string('legal_representative');
            $table->unsignedBigInteger('picture_profile')->nullable();
            $table->unsignedBigInteger('picture_front')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->foreign('picture_profile')->references('id')->on('picture')->onDelete('cascade');
            $table->foreign('picture_front')->references('id')->on('picture')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner', function (Blueprint $table) {
            $table->dropConstrainedForeignId('picture_profile');
            $table->dropConstrainedForeignId('picture_front');
        });
    }
};
