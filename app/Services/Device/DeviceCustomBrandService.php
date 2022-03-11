<?php

namespace App\Services\Device;

use App\Common\Services\BaseService;
use App\Models\DeviceCustomBrandModel;

class DeviceCustomBrandService extends BaseService
{

    public $source = 'CUSTOM';


    public function __construct(){
        parent::__construct();
        $this->model = new DeviceCustomBrandModel();
    }


    /**
     * @param $model
     * @return mixed|string
     * 获取品牌枚举
     */
    public function getBrand($model){
        $info = $this->model->where('model',$model)->first();
        $brand = '';
        if(!empty($info)){
            $brand = $info->brand;
        }
        return $brand;
    }


    public function save($model,$brand){
        $info = $this->model->where('model',$model)->first();
        if(empty($info)){
            $info = (new DeviceCustomBrandModel());
        }
        $info->model = $model;
        $info->brand = $brand;
        $info->save();
        return $info;
    }

}
