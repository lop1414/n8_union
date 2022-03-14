<?php

namespace App\Services\Ua;

use App\Common\Services\BaseService;


class UaDeviceService extends BaseService
{

    /**
     * @var
     */
    protected $deviceModel;


    protected $service;


    public function __construct(){
        parent::__construct();
        $this->service = new V1UaDeviceService();
    }



    public function read($deviceModel = ''){
        if(!empty($deviceModel)){
            $this->setDeviceModel($deviceModel);
        }

        $info = $this->service->read($this->deviceModel);
        return $info;
    }



    public function setDeviceModel($deviceModel){
        $this->deviceModel = $deviceModel;
        return $this;
    }



    public function update($deviceModel = ''){
        if(!empty($deviceModel)){
            $this->setDeviceModel($deviceModel);
        }

        $info = $this->service->update($this->deviceModel);
        return $info;
    }

}
