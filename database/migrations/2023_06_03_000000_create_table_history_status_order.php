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
        Schema::create('history_status_order', function (Blueprint $table) {
            $table->unsignedBigInteger('sale')->nullable();
            $table->foreign('sale')->references('id')->on('sales')->onDelete('cascade');
            $table->unsignedBigInteger('status')->nullable();
            $table->foreign('status')->references('id')->on('status_order')->onDelete('cascade');
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
        Schema::dropIfExists('history_status_order', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sale');
            $table->dropConstrainedForeignId('status');
        });
    }
};
