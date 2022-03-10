<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Services\DeviceNetworkLicenseService;

class SyncDeviceNetworkLicenseCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'sync_device_network_license {--year=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '同步设备入网许可信息';



    public function handle(){

        $year = $this->option('year') ?: date('Y');

        $key = "sync_device_network_license|{$year}";

        $this->lockRun(function () use ($year){
            (new DeviceNetworkLicenseService())->syncDeviceInfo($year);
        },$key, 60*60*3,['log' => true]);
    }


}
