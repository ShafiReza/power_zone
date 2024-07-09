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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('regular_customer_id')->nullable();
            $table->unsignedBigInteger('irregular_customer_id')->nullable();
            $table->string('customer_type');
            $table->string('customer_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();

            $table->foreign('regular_customer_id')->references('id')->on('regular_customers')->onDelete('cascade');
            $table->foreign('irregular_customer_id')->references('id')->on('irregular_customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
