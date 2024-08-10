<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create('payment_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->date('receive_date')->nullable();
            $table->text('description')->nullable();
            $table->decimal('bill_amount', 8, 2);
            $table->decimal('receivable_amount', 8, 2);
            $table->decimal('due_amount', 8, 2);
            $table->timestamps();

            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_histories');
    }
}

