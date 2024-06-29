<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('regular_customer_id')->nullable();
            $table->unsignedBigInteger('irregular_customer_id')->nullable();
            $table->string('customer_name');
            $table->string('bill_type');
            $table->date('bill_date');
            $table->decimal('final_amount', 10, 2);
            $table->decimal('amount', 10, 2);
            $table->timestamps();

            $table->foreign('regular_customer_id')->references('id')->on('regular_customers')->onDelete('cascade');
            $table->foreign('irregular_customer_id')->references('id')->on('irregular_customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bills');
    }
}
