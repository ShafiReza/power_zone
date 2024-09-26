<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\MonthlyBill;
class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\GenerateMonthlyBills::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('generatemonthlybills:cron')->monthlyOn(1, '00:00');
        $schedule->call(function () {
            MonthlyBill::where('status', 'pending')->each->updateStatus();
        })->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

}
