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
         Schema::create('bill_items', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('bill_id');
        $table->string('product_name');
        $table->text('description')->nullable();
        $table->integer('quantity');
        $table->decimal('unit_price', 10, 2);
        $table->decimal('discount', 10, 2)->nullable();
        $table->string('discount_type')->nullable();
        $table->decimal('total_amount', 10, 2);
        $table->timestamps();

        $table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_items');
    }
};
