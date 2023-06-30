<?php

namespace App\Services\Ua;

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




    public function getUa(){
        return $this->ua;
    }



    public function setUa($ua){
        $this->ua = $ua;

        // 清除设备型号、系统版本 信息
        $this->deviceModel = '';
        $this->sysVersion = '';
        return $this;
    }


    public function getInfo($ua = ''){
        if(!empty($ua)){
            $this->setUa($ua);
        }
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
            if(isset($arrMatches[2][0])) {
                $tmp = explode('iPhone OS', $arrMatches[2][0]);
                $sysVersion = explode('like', $tmp[1]);
                $this->sysVersion = trim($sysVersion[0]);
            }

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





}
