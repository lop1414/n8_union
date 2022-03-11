<?php

namespace App\Services\Device;

use App\Common\Services\BaseService;
use App\Models\DeviceCustomNameModel;

class DeviceCustomNameService extends BaseService
{

    public $source = 'CUSTOM';


    public function __construct(){
        parent::__construct();
        $this->model = new DeviceCustomNameModel();
    }


    /**
     * @param $model
     * @return mixed|string
     * 获取品牌枚举
     */
    public function getName($model){
        $info = $this->model->where('model',$model)->first();
        $name = '';
        if(!empty($info)){
            $name = $info->name;
        }
        return $name;
    }



    public function save($model,$name){
        $info = $this->model->where('model',$model)->first();
        if(empty($info)){
            $info = (new DeviceCustomNameModel());
        }
        $info->model = $model;
        $info->name = $name;
        $info->save();
        return $info;
    }

}
