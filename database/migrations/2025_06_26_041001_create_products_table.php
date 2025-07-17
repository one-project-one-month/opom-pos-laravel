<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('sku');
            $table->integer('price');
            $table->integer('const_price');
            $table->integer('stock');
            $table->integer('brand_id');
            $table->integer('category_id');
            $table->foreignId('discount_item_id')->nullable()->constrained('discount_items')
            ->nullOnUpdate()
            ->nullOnDelete();
            $table->string('photo')->nullable();
            $table->date('expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('products');
        Schema::create('products', function (Blueprint $table){
            $table->dropForeign(['discount_item_id']);
            $table->foreignId('discount_item_id')->nullable()           ->constrained('discount_items') 
            ->references('id')
            ->onDelete('restrict');
                  
            

        });
    }
};
