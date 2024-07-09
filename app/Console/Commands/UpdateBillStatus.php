<?php

namespace App\Console\Commands;
use App\Models\MonthlyBill;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateBillStatus extends Command
{
    protected $signature = 'update:billstatus';
    protected $description = 'Update the status of bills to due if not paid within a month';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
{
    $bills = MonthlyBill::where('status', 'pending')->get();

    foreach ($bills as $bill) {
        $startDate = Carbon::parse($bill->start_date);
        if ($startDate->diffInMonths(Carbon::now()) >= 1) {
            $bill->update(['status' => 'due']);
        }
    }
}
}
