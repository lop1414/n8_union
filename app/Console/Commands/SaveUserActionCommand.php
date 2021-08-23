<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Helpers\Functions;
use App\Common\Services\ConsoleEchoService;

class SaveUserActionCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'save_user_action {--action=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '队列行为数据入库';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }



    public function handle(){
        $action    = $this->option('action');
        if(is_null($action)){
            (new ConsoleEchoService())->error('action 参数必传');
            return ;
        }


        $action = ucfirst(Functions::camelize($action));
        $class = "App\Services\SaveUserAction\\Save{$action}ActionService";

        if(!class_exists($class)){
            (new ConsoleEchoService())->error("{$class} 类不存在");
            return ;
        }

        $service = new $class;

        $key = 'save_user_action|'.$action;
        $this->lockRun(function () use ($service,$action){
            $service->run();
        },$key,60*60,['log' => true]);
    }


}
