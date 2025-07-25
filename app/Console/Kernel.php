<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\SyncBloodInventory::class,
        \App\Console\Commands\GenerateStockAlerts::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sync:blood-inventory')
                 ->hourly();
    }
} 