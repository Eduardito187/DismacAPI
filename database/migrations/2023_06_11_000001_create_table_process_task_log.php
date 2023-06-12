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
        Schema::create('process_task_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_process_task')->nullable();
            $table->foreign('id_process_task')->references('id')->on('process_task')->onDelete('cascade');
            $table->string('mensaje')->nullable();
            $table->boolean('status');
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
        Schema::dropIfExists('process_task_log', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_process_task');
        });
    }
};
