<?php

namespace App\Services\Ua;



class V1UaDeviceService extends UaDeviceBaseService
{

    protected $version = 1;



    public function getDeviceInfo($deviceModel){
        $extend = ['version' => $this->version];

        // 品牌
        $brand  = '';
        $customBrand = $this->getUaDeviceCustomBrand($deviceModel);
        if(!empty($customBrand)){
            $extend['ua_device_custom_brand'] = $customBrand;
            $brand = $customBrand['brand'];
        }

        if(empty($brand)){
            // 通过进网许可获取
            $deviceNetworkLicenseInfo = $this->getDeviceNetworkLicenseInfo($deviceModel);
            $extend['device_network_license'] = $deviceNetworkLicenseInfo;
            $brand = $deviceNetworkLicenseInfo['brand'] ?? '';
        }

        // 名称
        $name  = '';
        $customName = $this->getUaDeviceCustomName($deviceModel);
        if(!empty($customName)){
            $extend['ua_device_custom_name'] = $customName;
            $name = $customName['name'];
        }

        return [
            'name'   => $name,
            'brand'  => $brand,
            'extend' => $extend
        ];
    }


}
