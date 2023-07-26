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
        Schema::create('social_campaings', function (Blueprint $table) {
            $table->unsignedBigInteger('id_social_network')->nullable();
            $table->foreign('id_social_network')->references('id')->on('social_network')->onDelete('cascade');
            $table->unsignedBigInteger('id_campaign')->nullable();
            $table->foreign('id_campaign')->references('id')->on('campaign')->onDelete('cascade');
            $table->string('url', 250);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('social_campaings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_social_network');
            $table->dropConstrainedForeignId('id_campaign');
        });
    }
};
