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
        Schema::create('campaign', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger('id_social_network')->nullable();
            $table->foreign('id_social_network')->references('id')->on('social_network')->onDelete('cascade');
            $table->unsignedBigInteger('id_partner')->nullable();
            $table->foreign('id_partner')->references('id')->on('partner')->onDelete('cascade');
            $table->unsignedBigInteger('id_category')->nullable();
            $table->foreign('id_category')->references('id')->on('category')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('url', 250);
            $table->boolean('status');
            $table->integer('products');
            $table->timestamp('created_at');
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('from_at')->nullable();
            $table->timestamp('to_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaign', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_social_network');
            $table->dropConstrainedForeignId('id_partner');
            $table->dropConstrainedForeignId('id_category');
        });
    }
};
