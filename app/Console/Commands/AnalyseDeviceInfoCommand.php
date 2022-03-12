<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Helpers\Functions;
use App\Services\Device\DeviceBrandService;
use App\Services\Device\DeviceNameService;

class AnalyseDeviceInfoCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'analyse_device_info {--type=} {--time=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '分析设备信息';



    public function handle(){

        $type = $this->option('type');
        if(is_null($type)){
            echo 'type 参数必传';
            return ;
        }

        $time = $this->option('time');
        $startTime = $endTime = '';
        if(!empty($time)) {
            list($startTime,$endTime) = Functions::getTimeRange($time);
        }



        $key = "analyse_device_info|{$type}";

        $this->lockRun(function () use ($type,$startTime,$endTime){
            switch ($type){
                case 'naem':
                    (new DeviceNameService())->analyse($startTime,$endTime);
                    break;
                case 'brand':
                    (new DeviceBrandService())->analyse($startTime,$endTime);
            }
        },$key, 60*60*3,['log' => true]);
    }


}
