<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Helpers\Functions;
use App\Common\Services\ConsoleEchoService;
use App\Services\N8UnionUserService;

class AnalyseUserDeviceBrandCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'analyse_user_device_brand {--product_id=}  {--time=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '用户设备品牌分析';

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

        $productId = $this->option('product_id') ?: 0;
        $brand = $this->option('brand') ?: '';
        $time = $this->option('time');
        list($startTime,$endTime) = Functions::getTimeRange($time);

        $key = "analyse_user_device_brand|{$brand}|{$productId}";

        $this->lockRun(function () use ($startTime,$endTime,$brand,$productId){
            (new N8UnionUserService())->analyseDeviceBrand($startTime,$endTime,$brand,$productId);
        },$key, 60*60*3,['log' => true]);
    }


}
