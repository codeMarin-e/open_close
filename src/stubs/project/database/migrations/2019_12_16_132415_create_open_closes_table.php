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
        Schema::create('open_closes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('owner_id');
            $table->string('owner_type');
            $table->string('type');
            $table->unsignedBigInteger('type_value');
            $table->string('open_at')->nullable();
            $table->string('close_at')->nullable();
            $table->boolean('close_next_day')->default(0);
            $table->timestamps();

            $table->index(['owner_id', 'owner_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('open_closes');
    }
};
