<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TrainOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('train_options', function (Blueprint $table) {
            $table->id();
            $table->integer('train_id');
            $table->integer('train_seats_count_x');
            $table->integer('train_seats_count_y');
            $table->enum('available_class', ['econom', 'business']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('train_options');
    }
}
