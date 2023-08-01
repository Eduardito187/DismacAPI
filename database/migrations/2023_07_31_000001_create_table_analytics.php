<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('analytics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('channel', 50)->nullable();
            $table->string('medium', 50)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('code', 50)->nullable();
            $table->string('key', 50)->nullable();
            $table->string('value')->nullable();
            $table->boolean('status')->nullable();
            $table->unsignedBigInteger('id_partner')->nullable();
            $table->foreign('id_partner')->references('id')->on('partner')->onDelete('cascade');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
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
        Schema::dropIfExists('analytics', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_partner');
        });
    }
};