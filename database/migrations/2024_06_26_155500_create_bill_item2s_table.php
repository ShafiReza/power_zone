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
        Schema::create('bill_item2s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->string('discount_type');
            $table->decimal('discount', 8, 2);
            $table->decimal('vat', 8, 2);
            $table->decimal('receivable_amount', 10, 2)->default(0); // Add this field
            $table->decimal('due_amount', 10, 2)->default(0); // Add this field
            $table->decimal('final_amount', 10, 2);
            $table->timestamps();

            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_item2s');
    }
};
