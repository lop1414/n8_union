<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Services\Device\DeviceNetworkLicenseService;

class SyncDeviceNetworkLicenseCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'sync_device_network_license {--year=} {--company=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '同步设备入网许可信息';



    public function handle(){

        $year = $this->option('year') ?: date('Y');
        $company = $this->option('company') ?: '';

        $key = "sync_device_network_license|{$year}";

        $this->lockRun(function () use ($year,$company){
            (new DeviceNetworkLicenseService())->syncDeviceInfo($year,$year,$company);
        },$key, 60*60*3,['log' => true]);
    }


}
