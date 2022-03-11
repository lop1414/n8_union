<?php
namespace App\Http\Controllers\Admin;

use App\Models\DeviceNameModel;
use App\Services\Device\DeviceCustomNameService;
use Illuminate\Http\Request;

class DeviceNameController extends BaseController
{

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new DeviceNameModel();

        parent::__construct();
    }



    /**
     * @param Request $request
     * @return mixed
     * 更新
     */
    public function update(Request $request){
        $requestData = $request->all();
        $service = new DeviceCustomNameService();

        $requestData['source'] = $service->source;
        unset($requestData['model']);
        $this->curdService->setRequestData($requestData);
        $this->curdService->addField('name')->addValidRule('required');

        $ret = $this->curdService->update();

        $deviceNameModel = $this->curdService->getModel();

        // 更新自定义品牌表
        $service->save($deviceNameModel->model,$deviceNameModel->name);

        return $this->ret($ret, $deviceNameModel);
    }

}
