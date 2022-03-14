<?php
namespace App\Http\Controllers\Admin;

use App\Common\Enums\DeviceBrandEnum;
use App\Models\UaDeviceCustomBrandModel;
use App\Models\UaDeviceCustomNameModel;
use App\Models\UaDeviceModel;
use App\Services\Ua\UaDeviceService;
use Illuminate\Http\Request;

class UaDeviceController extends BaseController
{

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new UaDeviceModel();

        parent::__construct();
    }




    /**
     * @param Request $request
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     * 更新
     */
    public function update(Request $request){
        $requestData = $request->all();

        unset($requestData['model']);
        $this->curdService->setRequestData($requestData);
        $this->curdService->addField('name')->addValidRule('required');
        $this->curdService->addField('brand')
            ->addValidEnum(DeviceBrandEnum::class)
            ->addValidRule('required');


        $info = $this->curdService->read();

        $hasChange = false;

        //名称
        if($requestData['name'] != $info->name ){
            $customNameInfo = (new UaDeviceCustomNameModel())->where('model',$info->model)->first();
            if(empty($customNameInfo)){
                $customNameInfo = new UaDeviceCustomNameModel();
                $customNameInfo->model = $info->model;
            }
            $customNameInfo->name = $requestData['name'];
            $customNameInfo->save();
            $hasChange = true;
        }

        // 品牌
        if($requestData['brand'] != $info->brand ){
            $customBrandInfo = (new UaDeviceCustomBrandModel())->where('model',$info->model)->first();
            if(empty($customBrandInfo)){
                $customBrandInfo = new UaDeviceCustomNameModel();
                $customBrandInfo->model = $info->model;
            }
            $customBrandInfo->brand = $requestData['brand'];
            $customBrandInfo->save();

            $hasChange = true;
        }


        if($hasChange){
            $info = (new UaDeviceService())->update($info->model);
        }


        return $this->ret($info, $info);
    }

}
