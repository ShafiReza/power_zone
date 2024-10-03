<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('bill_id')->nullable()->after('id'); // Add the 'bill_id' column
            $table->foreign('bill_id')->references('id')->on('bills')->onDelete('cascade'); // Define foreign key relationship
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['bill_id']); // Drop foreign key
            $table->dropColumn('bill_id'); // Drop the column if necessary
        });
    }
};
