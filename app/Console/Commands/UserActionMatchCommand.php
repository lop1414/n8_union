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
    protected $signature = 'user_action_match {--adv_alias=} {--action=} {--time=} {--debug=} {--n8_guid=}';

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
        $debug     = $this->option('debug');
        if(is_null($action)){
            $this->consoleEchoService->error('action 参数必传');
            return ;
        }

        if(is_null($advAlias)){
            $this->consoleEchoService->error('adv_alias 参数必传');
            return ;
        }

        $time = $this->option('time');
        list($startTime,$endTime) = Functions::getTimeRange($time);

        $action = ucfirst(Functions::camelize($action));
        $class = "App\Services\UserActionMatch\\{$action}ActionMatchService";

        if(!class_exists($class)){
            $this->consoleEchoService->error("{$class} 类不存在");
            return ;
        }

        $service = new $class;
        $service->setAdvAlias($advAlias);
        $service->setTimeRange($startTime,$endTime);
        if($debug){
            $service->openDebug();
        }

        $key = "user_action_match|{$action}|{$advAlias}";

        $this->lockRun(function () use ($service,$action){
            $param = [];
            $n8Guid  = $this->option('n8_guid');
            if($n8Guid){
                $param['n8_guid'] = $n8Guid;
            }
            $service->run($param);
        },$key, 60*60*3,['log' => true]);
    }


}
