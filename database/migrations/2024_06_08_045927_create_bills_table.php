<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('bill_type');
            $table->date('bill_date');
            $table->decimal('amount', 10, 2);
            $table->string('status')->default('active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bills');
    }
}

class CreateBillItemsTable extends Migration
{
    public function up()
    {
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('bills')->onDelete('cascade');
            $table->string('product_name');
            $table->text('description')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->foreignId('customer_id')->constrained(); // Example foreign key constraint
            $table->string('bill_type');
            $table->date('bill_date');
            $table->decimal('final_amount', 10, 2); // Ensure this line is present
            $table->timestamps();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

        });
    }

    public function down()
    {
        Schema::dropIfExists('bill_items');
    }
}

