<?php

namespace App\Console;

use App\Console\Commands\CreateTableCommand;
use App\Console\Commands\UserActionDataToDbCommand;
use App\Console\Commands\UserActionMatchCommand;
use App\Console\Commands\Yw\PullBookCommand;
use App\Console\Commands\Yw\PullChapterCommand;
use App\Console\Commands\Bm\PullChannelCommand;
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
        // 创建table
        CreateTableCommand::class,

        // 用户行为数据
        UserActionDataToDbCommand::class,

        // 用户行为匹配
        UserActionMatchCommand::class,


        // 阅文快应用
        PullBookCommand::class,
        PullChapterCommand::class,

        // 笔墨
        PullChannelCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('create_table')->cron('0 0 1,15 * *');

        $schedule->command('bm:pull_cp_channel --date=today')->cron('55 23 * * *');

    }
}
