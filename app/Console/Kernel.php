<?php

namespace App\Console;

use App\Console\Commands\RefreshCacheCommand;
use App\Console\Commands\Yw\PullBookCommand;
use App\Console\Commands\Yw\PullChapterCommand;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // 缓存
        RefreshCacheCommand::class,

        // 阅文快应用
        PullBookCommand::class,
        PullChapterCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
