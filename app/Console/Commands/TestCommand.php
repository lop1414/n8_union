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
        $list = (new DeviceModel())->where('brand','')->whereNull('has_network_license')->get();
        foreach ($list as $item){
            $item->brand = $service->getBrand($item->model);
            $hasNetworkLicense =  null;
            if(empty($item->brand)){
                $url = 'https://jwxk.miit.gov.cn/dev-api-20/internetService/CertificateQuery';
                $param = array(
                    'equipmentModel' => $item->model,
                    'pageNo'         => 1,
                    'pageSize'       => 100,
                    'isphoto'        => 2
                );

                $ret = file_get_contents($url .'?'. http_build_query($param));
                $result = json_decode($ret, true);
                if($result['code'] == 200){
                    foreach ($result['data']['records'] as $v){
                        if(strtoupper($v['equipmentModel']) == strtoupper($item->model)){
                            var_dump($result,$item->model);
                            $hasNetworkLicense = 1;
                        }
                    }
                }
            }
            $item->has_network_license = $hasNetworkLicense;
            $item->save();
        }
    }



}
