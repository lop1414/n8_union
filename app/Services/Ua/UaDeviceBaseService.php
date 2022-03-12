<?php

namespace App\Services\Ua;

use App\Common\Services\BaseService;
use App\Models\DeviceNetworkLicenseModel;
use App\Models\UaDeviceCustomModel;
use App\Models\UaDeviceLogModel;
use App\Models\UaDeviceModel;
use App\Services\DeviceNetworkLicenseService;

class UaDeviceBaseService extends BaseService
{

    /**
     * @var
     * 版本号
     */
    protected $version;



    public function __construct(){
        parent::__construct();
        $this->model = new UaDeviceModel();
    }



    public function read($deviceModel){
        $info = $this->model->where('model',$deviceModel)->first();
        if(empty($info)){
            $info =$this->add($deviceModel);
        }
        return $info;
    }


    public function add($deviceModel){
        $deviceInfo = $this->getDeviceInfo($deviceModel);
        $info = new UaDeviceModel();
        $info->model = $deviceModel;
        $info->name  = $deviceInfo['name'];
        $info->brand = $deviceInfo['brand'];
        $info->save();

        $log = new UaDeviceLogModel();
        $log->ua_device_id = $info->id;
        $log->model = $info->name;
        $log->name = $info->brand;
        $log->extend = array_merge(['version' => $this->version],$deviceInfo['extend']);
        $log->save();
        return $info;
    }

    public function getDeviceInfo($deviceModel){
        return [
            'name'   => '',
            'brand'  => '',
            'extend' => []
        ];
    }

    public function getUaDeviceCustomInfo($deviceModel){
        $info = (new UaDeviceCustomModel())->where('model',$deviceModel)->first();
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
