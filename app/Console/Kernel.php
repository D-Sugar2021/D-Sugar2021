<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\AutoSubmitExams; // Import the command

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        AutoSubmitExams::class, // Register the command
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // Schedule the exams:auto-submit command to run every minute
        $schedule->command('exams:auto-submit')
                 ->everyMinute()
                 ->withoutOverlapping() // Prevents the command from running if the previous instance is still running
                 ->runInBackground(); // Allows other scheduled tasks to run without waiting for this one

        // Example of logging scheduled task execution:
        // $schedule->command('exams:auto-submit')->everyMinute()->appendOutputTo(storage_path('logs/scheduler.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
