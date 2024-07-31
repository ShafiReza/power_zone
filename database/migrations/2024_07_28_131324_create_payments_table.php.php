<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('monthly_bills')->onDelete('cascade');
            $table->string('description');
            $table->decimal('receiveable_amount', 10, 2);
            $table->decimal('due_amount', 10, 2);
            $table->timestamps();
        });

        // Create a trigger to check if the bill ID is for a monthly bill
        // DB::statement('
        //     CREATE TRIGGER trg_payments_before_insert
        //     BEFORE INSERT ON payments
        //     FOR EACH ROW
        //     BEGIN
        //         IF NOT EXISTS (SELECT 1 FROM bills WHERE id = NEW.bill_id AND bill_type = \'monthly\') THEN
        //             SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'Cannot insert payment for non-monthly bill\';
        //         END IF;
        //     END;
        // ');

        // DB::statement('
        //     CREATE TRIGGER trg_payments_before_update
        //     BEFORE UPDATE ON payments
        //     FOR EACH ROW
        //     BEGIN
        //         IF NOT EXISTS (SELECT 1 FROM bills WHERE id = NEW.bill_id AND bill_type = \'monthly\') THEN
        //             SIGNAL SQLSTATE \'45000\' SET MESSAGE_TEXT = \'Cannot update payment for non-monthly bill\';
        //         END IF;
        //     END;
        // ');
    }

    public function down()
    {
        // Drop the triggers
        // DB::statement('DROP TRIGGER trg_payments_before_insert');
        // DB::statement('DROP TRIGGER trg_payments_before_update');

        // Drop the foreign key constraint on the payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign('payments_bill_id_foreign');
        });

        // Drop the payments table
        Schema::dropIfExists('payments');

        // Drop the bills table
        Schema::dropIfExists('bills');
    }
}
