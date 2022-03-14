<?php

namespace App\Services\Ua;

use App\Common\Services\BaseService;
use App\Datas\UaDeviceData;
use App\Models\DeviceNetworkLicenseModel;
use App\Models\UaDeviceCustomBrandModel;
use App\Models\UaDeviceCustomNameModel;
use App\Services\DeviceNetworkLicenseService;

class UaDeviceBaseService extends BaseService
{

    /**
     * @var
     * 版本号
     */
    protected $version;




    public function read($deviceModel){
        $modelData = new UaDeviceData();
        $info = $modelData->setParams(['model' => $deviceModel])->read();

        if(empty($info)){
            $deviceInfo = $this->getDeviceInfo($deviceModel);

            $data = [
                'device_model' => $deviceModel,
                'name'         => $deviceInfo['name'],
                'brand'        => $deviceInfo['brand'],
            ];
            $logExtend = $deviceInfo['extend'];
            $info = $modelData->create($data,$logExtend);

        }
        return $info;
    }



    public function getDeviceInfo($deviceModel){
        return [
            'name'   => '',
            'brand'  => '',
            'extend' => []
        ];
    }



    public function getUaDeviceCustomBrand($deviceModel){
        $info = (new UaDeviceCustomBrandModel())->where('model',$deviceModel)->first();
        return $info ? $info->toArray() : [];
    }


    public function getUaDeviceCustomName($deviceModel){
        $info = (new UaDeviceCustomNameModel())->where('model',$deviceModel)->first();
        return $info ? $info->toArray() : [];
    }



    public function getDeviceNetworkLicenseInfo($deviceModel){
        $info = (new DeviceNetworkLicenseModel())->where('model',$deviceModel)->first();
        if(!empty($info)){
            $info = $info->toArray();
            $info['brand'] = (new DeviceNetworkLicenseService())->getBrandByCompany($info['apply_org']);
        }
        return $info ?: [];
    }


}
