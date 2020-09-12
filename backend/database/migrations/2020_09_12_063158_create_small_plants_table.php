<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmallPlantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('small_plants', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('latin_name')->nullable();
            $table->string('common_name')->nullable();
            $table->string('moisture')->nullable();
            $table->integer('medicinal')->nullable();
            $table->string('habit')->nullable();
            $table->float('width')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('small_plants');
    }
}
