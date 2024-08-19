<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('monthly_bills')->onDelete('cascade');
            $table->date('receive_date')->nullable();
            $table->string('description');
            $table->decimal('receivable_amount', 10, 2); // corrected typo
            $table->decimal('due_amount', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign('payments_bill_id_foreign');
        });

        Schema::dropIfExists('payments');
    }
}

