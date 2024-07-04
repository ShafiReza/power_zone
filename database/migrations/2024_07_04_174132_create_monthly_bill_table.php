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
        Schema::create('monthly_bill', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('regular_customer_id');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('pending'); // pending, paid, due
            $table->year('billing_year');
            $table->timestamps();

            $table->foreign('regular_customer_id')->references('id')->on('regular_customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_bill');
    }
};
