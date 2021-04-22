<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('jobs-log:handle')->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('sites-log:handle')->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('quotes-log:handle')->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('invoices-log:handle')->everyMinute()->withoutOverlapping()->runInBackground();
        $schedule->command('simpro:handle-jobs')->everyMinute()->withoutOverlapping();
        $schedule->command('clear:set-password-hash')->hourly();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
