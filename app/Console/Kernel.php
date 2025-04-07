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
        // Track mentions every 15 minutes
        $schedule->command('mentions:track --all')
            ->everyFifteenMinutes()
            ->withoutOverlapping();

        // Send daily digests at 8 AM
        $schedule->command('mentions:send-daily-digests')
            ->dailyAt('08:00')
            ->withoutOverlapping();
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