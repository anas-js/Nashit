<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // var_dump(now()->format('Y-m-d H:i:s'));
        // $schedule->command('email:summary')->everyMinute();
        //  $schedule->command('email:summary')->cron("* * * * *")->withoutOverlapping()->timezone('America/Campo_Grande');
          $schedule->command('email:summary')->hourly();
        //  $schedule->command('email:summary')->everyMinute();
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
