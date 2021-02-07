<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Services\ConsoleEchoService;
use App\Services\TableCache\N8GlobalOrderTableCacheService;
use App\Services\TableCache\N8GlobalUserTableCacheService;

class RefreshCacheCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'refresh_cache {--type=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '刷新缓存';

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
        $type    = $this->option('type');
        if(is_null($type)){
            $this->consoleEchoService->error('type 参数必传');
            return ;
        }


        $this->lockRun(function () use ($type){
            switch ($type){
                case 'global_user':
                    $this->globalUserInfo();
                    break;
                case 'global_order':
                    $this->globalOrderInfo();
                    break;
            }

        },$type,60 * 60,['log' => true]);
    }


    /**
     * 全局用户信息
     */
    public function globalUserInfo(){
        $service = new N8GlobalUserTableCacheService();

        $service->refreshAllCache();
    }

    /**
     * 全局订单信息
     */
    public function globalOrderInfo(){
        $service = new N8GlobalOrderTableCacheService();

        $service->refreshAllCache();
    }


}
