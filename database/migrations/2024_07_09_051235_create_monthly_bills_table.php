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
        Schema::create('monthly_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('regular_customer_id');
            $table->string('customer_address');
            $table->decimal('amount', 10, 2);
            $table->enum('service', ['lift', 'generator', 'lift and generator']);
            $table->string('bill_month'); // Ensure this is a date field
            $table->date('start_date'); // Ensure this is a date field
            $table->date('next_generation_date')->nullable();
            $table->enum('status', ['pending', 'paid', 'due']);
            $table->timestamps();

            $table->foreign('regular_customer_id')->references('id')->on('regular_customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_bills');
    }
};
