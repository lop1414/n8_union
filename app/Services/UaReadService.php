<?php

namespace App\Services;


use App\Common\Enums\DeviceBrandEnum;
use App\Models\DeviceNetworkLicenseModel;

class UaReadService
{

    /**
     * @var
     */
    protected $ua;


    /**
     * @var
     * 设备型号
     */
    protected $deviceModel;


    /**
     * @var
     * 系统版本
     */
    protected $sysVersion;



    /**
     * @var
     * 缓存有效期
     */
    protected $ttl = 86400;


    protected  $brandEnumMap;

    public function getInfo(){
        $this->analyseDeviceInfo();
        return [
            'device_model'  => $this->deviceModel,
            'sys_version'   => $this->sysVersion,
        ];
    }

    public function analyseDeviceInfo(){

        $strPattern = "/Mozilla\/5.0\s*\([^\(\)]*?(Windows[^\(\)]*?|Android[^\(\)]*?|iPhone);\s*([^\(\)]*?)\)/";
        $arrMatches = [];
        $ua = $this->getUa();
        preg_match_all($strPattern, $ua, $arrMatches);


        if(strpos($ua,'iPhone') !== false){
            $this->deviceModel = 'iPhone';
            //系统版本
            $tmp = explode('iPhone OS',$arrMatches[2][0]);
            $sysVersion =  explode('like',$tmp[1]);
            $this->sysVersion = trim($sysVersion[0]);

        }else{

            //系统版本
            $this->sysVersion = '';
            if(isset($arrMatches[1][0])){
                list(,$sysVersion) = explode(' ',$arrMatches[1][0]);
                $this->sysVersion = trim($sysVersion);
            }


            //型号
            $this->deviceModel = 'OTHER';
            if(isset($arrMatches[2][0])){
                list($deviceModel) = explode('Build',$arrMatches[2][0]);
                $deviceModel = strtoupper(trim($deviceModel));
                $deviceModel && $this->deviceModel = $deviceModel;
            }
        }
    }

    public function getUa(){
        return $this->ua;
    }

    public function setUa($agent){
        $this->ua = $agent;

        // 清除设备型号
        $this->deviceModel = '';
        return $this;
    }

    public function getBrand($model){
        $info = (new DeviceNetworkLicenseModel)->where('model',$model)->first();
        $company = empty($info) ? '' : $info->apply_org;

        // 根据公司名称映射匹配枚举
        return $this->brandEnumMap[$company] ?? '';
    }


}
