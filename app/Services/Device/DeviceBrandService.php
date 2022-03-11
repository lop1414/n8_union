<?php

namespace App\Services\Device;

use App\Models\DeviceBrandModel;


class DeviceBrandService extends DeviceBaseService
{

    protected $deviceNetworkLicenseService;
    protected $deviceCustomBrandService;
    /**
     * @var int
     * 版本号
     */
    private $version = 1;

    public function __construct(){
        parent::__construct();
        $this->model = new DeviceBrandModel();
        $this->deviceNetworkLicenseService = new DeviceNetworkLicenseService();
        $this->deviceCustomBrandService = new DeviceCustomBrandService();
    }


    public function analyse($startTime = '',$endTime = ''){
        $deviceModels = $this->getDeviceModel($startTime,$endTime);
        foreach ($deviceModels as $deviceModel){
            $info = $this->model
                ->where('model',$deviceModel)
                ->where('version',$this->version)
                ->first();

            if(!empty($info) && !empty($info->brand)){
                continue;
            }

            $brandInfo = $this->getBrandInfo($deviceModel);

            $where = ['model' => $deviceModel,'version'   => $this->version];
            $this->model->updateOrCreate($where, [
                    'brand'     => $brandInfo['brand'],
                    'source'    => $brandInfo['source']
                ]
            );
        }
    }


    /**
     * @param $deviceModel
     * @return array
     * 获取品牌信息
     */
    public function getBrandInfo($deviceModel){
        $res = array();
        $res['brand'] = $this->deviceNetworkLicenseService->getBrand($deviceModel);
        $res['source'] = $this->deviceNetworkLicenseService->source;
        if(empty($res['brand'])){
            $res['brand'] = $this->deviceCustomBrandService->getBrand($deviceModel);
            $res['source'] = $this->deviceCustomBrandService->source;
        }
        return $res;
    }



}
