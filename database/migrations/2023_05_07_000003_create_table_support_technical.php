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
        Schema::create('support_technical', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->unsignedBigInteger('id_account')->nullable();
            $table->foreign('id_account')->references('id')->on('account')->onDelete('cascade');
            $table->unsignedBigInteger('id_partner')->nullable();
            $table->foreign('id_partner')->references('id')->on('partner')->onDelete('cascade');
            $table->string('title', 100);
            $table->text('description');
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
        Schema::dropIfExists('support_technical', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_account');
            $table->dropConstrainedForeignId('id_partner');
        });
    }
};
