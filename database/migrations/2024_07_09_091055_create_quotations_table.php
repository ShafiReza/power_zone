<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('irregular_customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->date('quotation_date')->nullable();
            $table->decimal('final_amount', 10, 2)->default(0);
            $table->timestamps();

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
        Schema::dropIfExists('quotations');
    }
}
