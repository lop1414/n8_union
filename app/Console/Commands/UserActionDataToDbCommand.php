<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Helpers\Functions;
use App\Common\Services\ConsoleEchoService;
use App\Enums\QueueEnums;

class UserActionDataToDbCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'user_action_data_to_db {--action=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '队列行为数据入库';

    protected $consoleEchoService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
        $this->consoleEchoService = new ConsoleEchoService();
    }



    public function handle(){
        $action    = $this->option('action');
        if(is_null($action)){
            $this->consoleEchoService->error('action 参数必传');
            return ;
        }


        $action = Functions::camelize($action);
        $class = "App\Services\UserActionDataToDb\\{$action}ActionService";

        if(!class_exists($class)){
            $this->consoleEchoService->error("{$class} 类不存在");
            return ;
        }

        $service = new $class;

        // 打印
        $description = Functions::getEnumMapName(QueueEnums::class,$service->getQueueEnum());
        $this->consoleEchoService->echo($description);

        $expire = env('APP_DEBUG') ? 1 : 60 * 60;

        $this->lockRun(function () use ($service,$action){
            $service->run();
        },$action,$expire,['log' => true]);
    }


}
