<?php

declare(strict_types=1);

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        //
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load([
            app_path('Domain/DayOne/Commands'),
            app_path('Domain/DayTwo/Commands'),
            app_path('Domain/DayThree/Commands'),
            app_path('Domain/DayFour/Commands'),
            app_path('Domain/DayFive/Commands'),
            app_path('Domain/DaySix/Commands'),
        ]);

        require base_path('routes/console.php');
    }
}
