<?php

namespace App\Console\Commands;
use App\Models\MonthlyBill;
use App\Models\RegularCustomer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyBills extends Command
{
    protected $signature = 'generate:monthlybills';
    protected $description = 'Generate monthly bills for regular customers';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
{
    $customers = RegularCustomer::all();
    $currentDate = Carbon::now();

    foreach ($customers as $customer) {
        $latestBill = MonthlyBill::where('regular_customer_id', $customer->id)
            ->latest()
            ->first();

        if ($latestBill && $latestBill->next_generation_date <= $currentDate) {
            // Generate a new bill
            $newBill = MonthlyBill::create([
                'regular_customer_id' => $customer->id,
                'customer_address' => $customer->address,
                'amount' => 0, // Default amount, adjust as needed
                'service' => 'lift', // Default service, adjust as needed
                'bill_month' => $currentDate->format('Y-m'),
                'start_date' => $currentDate,
                'status' => 'pending',
                'next_generation_date' => $currentDate->addMonth(),
            ]);

            // Update the latest bill's next generation date
            $latestBill->next_generation_date = $newBill->next_generation_date;
            $latestBill->save();
        }
    }
}
}
