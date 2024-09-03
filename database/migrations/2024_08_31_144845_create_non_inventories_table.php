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
        Schema::create('non_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('details')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('origin')->nullable();
            $table->decimal('purchase_price', 8, 2)->nullable();
            $table->decimal('sell_price', 8, 2)->nullable();
            $table->decimal('wholesale_price', 8, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('total_amount', 8, 2);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('non_inventories');
    }
};
