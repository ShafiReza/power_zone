<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MonthlyBill;
use Carbon\Carbon;

class UpdateBillStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updatebillstatus:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the status of bills to due if not paid within a month';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bills = MonthlyBill::whereIn('status', ['pending', 'partial'])
            ->where('next_generation_date', '<=', Carbon::now())
            ->get();

        foreach ($bills as $bill) {
            if ($bill->status != 'paid') {
                $bill->status = 'due';
                $bill->save();
            }
        }
    }
}
