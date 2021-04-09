<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Helpers\Functions;
use App\Common\Services\ConsoleEchoService;

class UserActionMatchCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'user_action_match {--adv_alias=} {--action=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '行为数据匹配';

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
        $advAlias  = $this->option('adv_alias');
        if(is_null($action)){
            $this->consoleEchoService->error('action 参数必传');
            return ;
        }

        if(is_null($advAlias)){
            $this->consoleEchoService->error('adv_alias 参数必传');
            return ;
        }

        $action = Functions::camelize($action);
        $class = "App\Services\UserActionMatch\\{$action}ActionMatchService";

        if(!class_exists($class)){
            $this->consoleEchoService->error("{$class} 类不存在");
            return ;
        }

        $service = new $class;
        $service->setAdvAlias($advAlias);
        $service->setTimeRange('2021-01-01 00:00:00','2021-04-10 00:00:00');

        $service->run();die;
        $expire = env('APP_DEBUG') ? 1 : 60 * 60;

        $key = "user_action_match|{$action}|{$advAlias}";
        $this->lockRun(function () use ($service,$action){
            $service->run();
        },$key,$expire,['log' => true]);
    }


}
