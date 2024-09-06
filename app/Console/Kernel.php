<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        $schedule->command('sync:moysklad orders')
            ->hourlyAt([0, 30])
            ->runInBackground()
            ->withoutOverlapping();

        $schedule->command('sync:moysklad catalog')
            ->hourlyAt([10, 40])
            ->runInBackground()
            ->withoutOverlapping();

        $schedule->command('sync:moysklad stores')
            ->hourlyAt([15, 45])
            ->runInBackground()
            ->withoutOverlapping();

        $schedule->command('sync:moysklad counterparty')
            ->hourlyAt([16, 46])
            ->runInBackground()
            ->withoutOverlapping();

        $schedule->command('sync:moysklad stocks')
            ->hourlyAt([20, 50])
            ->runInBackground()
            ->withoutOverlapping();

        $schedule->command('sync:moysklad bundles')
            ->hourlyAt([22, 52])
            ->runInBackground()
            ->withoutOverlapping();
    }
    protected $commands = [
        \App\Console\Commands\SeedTestData::class,
    ];

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
