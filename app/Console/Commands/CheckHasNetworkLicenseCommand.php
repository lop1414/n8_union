<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Services\DeviceService;

class CheckHasNetworkLicenseCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'check_has_network_license';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '检查是否有网络许可证';



    public function handle(){

        (new DeviceService())->checkHasNetworkLicense();
    }


}
