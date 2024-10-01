<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MonthlyBill;
use App\Models\RegularCustomer;
use Carbon\Carbon;
use App\Http\Controllers\MonthlyBillController;
use Illuminate\Support\Facades\Log;
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
    protected $description = 'Generate monthly bills for regular customers on the first day of each month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $customers = RegularCustomer::all();
        $currentMonth = Carbon::now()->format('Y-m'); // Get current month in 'YYYY-MM' format
        $monthlyBillController = new MonthlyBillController();

        foreach ($customers as $customer) {
            // Check if a bill has already been generated for the current month
            $existingBill = MonthlyBill::where('regular_customer_id', $customer->id)
                ->where('bill_month', Carbon::now()->startOfMonth()) // Match the current month
                ->first();

            // Update the status of previous bills to "due" if not paid
            MonthlyBill::where('regular_customer_id', $customer->id)
                ->where('bill_month', '<', Carbon::now()->startOfMonth()) // Previous months
                ->whereNotIn('status', ['paid']) // Only update if not already paid
                ->update(['status' => 'due']);

            if (!$existingBill) {
                // No bill exists for the current month, so generate a new one

                // Generate the new bill
                $billDetails = $monthlyBillController->getBillDetails($customer);

                if ($billDetails && isset($billDetails['amount'], $billDetails['service'])) {
                    MonthlyBill::create([
                        'regular_customer_id' => $customer->id,
                        'customer_address' => $customer->address,
                        'amount' => $billDetails['amount'],
                        'description' => $billDetails['description'] ?? 'No description available',
                        'service' => $billDetails['service'],
                        'bill_month' => Carbon::now()->startOfMonth(), // Set bill month to the first day of the current month
                        'start_date' => Carbon::now(),
                        'status' => 'pending', // Current month's bill should be pending
                        'type' => 'initial',
                    ]);
                } else {
                    // Log or handle the case where bill details are missing or invalid
                    Log::error("Failed to generate bill for customer ID {$customer->id}: Missing or invalid bill details.");
                }
            }
        }
    }

}
