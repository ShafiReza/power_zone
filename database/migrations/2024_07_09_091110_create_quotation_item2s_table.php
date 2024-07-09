<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationItem2sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_item2s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->string('discount_type');
            $table->decimal('discount', 8, 2);
            $table->decimal('vat', 8, 2);
            $table->decimal('final_amount', 10, 2);
            $table->timestamps();

            $table->foreign('quotation_id')->references('id')->on('quotations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotation_item2s');
    }
}
