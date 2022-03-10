<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Services\ConsoleEchoService;
use App\Models\DeviceModel;
use App\Services\DeviceNetworkLicenseService;


class TestCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'test';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '';

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
        $service = new DeviceNetworkLicenseService();
        $list = (new DeviceModel())->where('brand','')->get();
        foreach ($list as $item){
            $item->brand = $service->getBrand($item->model);
            $item->save();
            echo $item->brand."\n";
        }
    }



}
