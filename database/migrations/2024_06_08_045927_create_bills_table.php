<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
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
            $table->string('customer_name')->nullable();
            $table->string('bill_type')->nullable();
            $table->date('bill_date')->nullable();
            $table->decimal('final_amount', 10, 2)->default(0);
            $table->decimal('amount', 10, 2)->default(0);
            $table->year('billing_year')->nullable();
            $table->date('billing_month')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('due_amount', 8, 2)->default(0);
            $table->string('type');
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
