<?php

namespace App\Console;

use App\Common\Enums\AdvAliasEnum;
use App\Console\Commands\SaveUserActionCommand;
use App\Console\Commands\TestCommand;
use App\Console\Commands\CreateTableCommand;
use App\Console\Commands\UserActionMatchCommand;
use App\Console\Commands\PullCpChannelCommand;
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

        TestCommand::class,
        // 创建table
        CreateTableCommand::class,

        // 用户行为数据
        SaveUserActionCommand::class,

        // 用户行为匹配
        UserActionMatchCommand::class,

        // 同步渠道
        PullCpChannelCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $time = time();
        $hourRange24 = date('Y-m-d H:i:s',$time - 60*60*24).','.date('Y-m-d H:i:s',$time);


        //创建分表
        $schedule->command('create_table')->cron('0 0 1,15 * *');

        //拉取CP渠道
        $schedule->command('pull_cp_channel --date=today')->cron('50 23 * * *');


        //行为数据入库
        $userAction = ['reg','follow','add_shortcut','order','complete_order','login','read'];
        foreach ($userAction as $action){
            $matchCommand = "save_user_action --action={$action}";
            $schedule->command($matchCommand)->cron('* * * * *');
        }


        //行为匹配
        $matchAdv = [AdvAliasEnum::OCEAN,AdvAliasEnum::BD,AdvAliasEnum::KS,AdvAliasEnum::UC,AdvAliasEnum::GDT];
        $matchAction = ['reg','follow','add_shortcut','order','complete_order'];
        foreach ($matchAdv as $advAlias){
            foreach ($matchAction as $action){
                $matchCommand = "user_action_match --adv_alias={$advAlias}  --time='{$hourRange24}' --action={$action}";
                $schedule->command($matchCommand)->cron('* * * * *');
            }
        }

    }
}
