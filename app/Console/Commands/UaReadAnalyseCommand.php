<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Helpers\Functions;
use App\Common\Services\ConsoleEchoService;
use App\Services\N8UnionUserService;

class UaReadAnalyseCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'ua_read_analyse {--product_id=} {--time=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '用户ua读取分析';

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
        $time = $this->option('time');
        list($startTime,$endTime) = Functions::getTimeRange($time);

        $key = "ua_read_analyse|{$productId}";

        (new N8UnionUserService())->batchUaReadAnalyse($startTime,$endTime,$productId);

//        $this->lockRun(function () use ($startTime,$endTime,$productId){
//            (new N8UnionUserService())->uaReadAnalyse($startTime,$endTime,$productId);
//        },$key, 60*60*3,['log' => true]);
    }


}
