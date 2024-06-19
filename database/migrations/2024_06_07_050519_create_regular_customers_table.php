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
        Schema::create('regular_customers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('');
            $table->string('phone');
            $table->string('address');
            $table->string('area');
            $table->string('city');
            $table->text('note')->nullable();
            $table->decimal('initial_bill_amount', 10, 2);
            $table->date('start_date');
            $table->date('next_bill_date');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regular_customers');
    }
};
