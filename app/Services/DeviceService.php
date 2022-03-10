<?php

namespace App\Services;

use App\Common\Services\BaseService;
use App\Models\DeviceModel;


class DeviceService extends BaseService
{

    /**
     * @throws \Exception
     * 检查是否有网络许可证
     */
    public function checkHasNetworkLicense(){
        $service = new DeviceNetworkLicenseService();
        $model = (new DeviceModel())->where('brand','')->whereNull('has_network_license');
        $lastId = 0;

        do{
            $list = $model->where('id','>',$lastId)->limit(1000)->get();
            foreach ($list as $item){
                $lastId = $item->id;

                $item->brand = $service->getBrand($item->model);
                $hasNetworkLicense =  0;
                if(empty($item->brand)){
                    echo$item->id.' : '. $item->model."\n";

                    $ret = $service->apiGetInfo('','','',$item->model);
                    foreach ($ret['records'] as $v){
                        if(strtoupper($v['equipmentModel']) == strtoupper($item->model)){
                            var_dump($v);
                            $hasNetworkLicense = 1;
                        }
                    }
                }
                $item->has_network_license = $hasNetworkLicense;
                $item->save();
            }
        }while(!$list->isEmpty());



    }



}
