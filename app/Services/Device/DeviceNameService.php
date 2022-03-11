<?php

namespace App\Services\Device;

use App\Models\DeviceNameModel;


class DeviceNameService extends DeviceBaseService
{

    protected $deviceCustomNameService;
    /**
     * @var int
     * 版本号
     */
    private $version = 1;

    public function __construct(){
        parent::__construct();
        $this->model = new DeviceNameModel();
        $this->deviceCustomNameService = new DeviceCustomNameService();
    }



    public function analyse($startTime = '',$endTime = ''){
        $deviceModels = $this->getDeviceModel($startTime,$endTime);
        foreach ($deviceModels as $deviceModel){
            $info = $this->model
                ->where('model',$deviceModel)
                ->where('version',$this->version)
                ->first();

            if(!empty($info) && !empty($info->name)){
                continue;
            }

            $deviceNameInfo = $this->getNameInfo($deviceModel);

            $where = ['model' => $deviceModel,'version' => $this->version];
            $this->model->updateOrCreate($where, [
                    'name'     => $deviceNameInfo['name'],
                    'source'   => $deviceNameInfo['source']
                ]
            );
        }
    }


    /**
     * @param $deviceModel
     * @return array
     * 获取设备名称信息
     */
    public function getNameInfo($deviceModel){
        $res = array();
        $res['name'] = $this->deviceCustomNameService->getName($deviceModel);
        $res['source'] = $this->deviceCustomNameService->source;
        return $res;
    }



}
