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
            app_path('Domain/Day01/Commands'),
            app_path('Domain/Day02/Commands'),
            app_path('Domain/Day03/Commands'),
            app_path('Domain/Day04/Commands'),
            app_path('Domain/Day05/Commands'),
            app_path('Domain/Day06/Commands'),
            app_path('Domain/Day07/Commands'),
            app_path('Domain/Day08/Commands'),
            app_path('Domain/Day09/Commands'),
        ]);

        require base_path('routes/console.php');
    }
}
