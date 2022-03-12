<?php

namespace App\Services\Ua;



class V1UaDeviceService extends UaDeviceBaseService
{

    protected $version = 1;



    public function getDeviceInfo($deviceModel){
        $name  = $brand  = '';
        $extend = [];
        $customInfo = $this->getUaDeviceCustomInfo($deviceModel);
        if(!empty($customInfo)){
            $extend['ua_device_custom'] = $customInfo;
            $name  = $customInfo['name'];
            $brand = $customInfo['brand'];
        }

        if(empty($brand)){
            $deviceNetworkLicenseInfo = $this->getDeviceNetworkLicenseInfo($deviceModel);
            $extend['device_network_license'] = $deviceNetworkLicenseInfo;
            $brand = $deviceNetworkLicenseInfo['brand'];
        }

        return [
            'name'   => $name,
            'brand'  => $brand,
            'extend' => $extend
        ];
    }


}
