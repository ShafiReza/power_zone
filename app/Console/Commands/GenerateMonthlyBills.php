<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MonthlyBill;
use App\Models\RegularCustomer;
use Carbon\Carbon;
use App\Http\Controllers\MonthlyBillController;


class GenerateMonthlyBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generatemonthlybills:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly bills for regular customers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $customers = RegularCustomer::all();
        $currentDate = Carbon::now();
        $monthlyBillController = new MonthlyBillController();

        foreach ($customers as $customer) {
            $latestBill = MonthlyBill::where('regular_customer_id', $customer->id)
                ->latest()
                ->first();

            if ($latestBill && $latestBill->next_generation_date <= $currentDate) {
                // Update the status of the previous bill to "due" if it's "Partial" or "pending"
                if (in_array($latestBill->status, ['Partial', 'pending'])) {
                    $latestBill->status = 'due';
                    $latestBill->save();
                }

                // Generate a new bill
                $billDetails = $monthlyBillController->getBillDetails($customer);

                MonthlyBill::create([
                    'regular_customer_id' => $customer->id,
                    'customer_address' => $customer->address,
                    'amount' => $billDetails['amount'],
                    'description' => $billDetails['description'],
                    'service' => $billDetails['service'],
                    'bill_month' => $currentDate,
                    'start_date' => $currentDate, // Use the latest bill's start_date
                    'next_generation_date' => $currentDate->copy()->addMonth(),
                    'status' => 'pending',
                    'type' => 'initial',
                ]);
            }
        }
    }
}
