<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('discount_items', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->integer('dis_percent');
        $table->date('start_date');
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('discount_items');
}

};
