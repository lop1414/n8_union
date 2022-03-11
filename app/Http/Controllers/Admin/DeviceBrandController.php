<?php
namespace App\Http\Controllers\Admin;


use App\Common\Enums\DeviceBrandEnum;
use App\Models\DeviceBrandModel;
use App\Services\Device\DeviceCustomBrandService;
use Illuminate\Http\Request;

class DeviceBrandController extends BaseController
{

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new DeviceBrandModel();

        parent::__construct();
    }



    /**
     * @param Request $request
     * @return mixed
     * 更新
     */
    public function update(Request $request){
        $requestData = $request->all();
        $service = new DeviceCustomBrandService();

        $requestData['source'] = $service->source;
        unset($requestData['model']);
        $this->curdService->setRequestData($requestData);
        $this->curdService->addField('brand')
            ->addValidEnum(DeviceBrandEnum::class)
            ->addValidRule('required');

        $ret = $this->curdService->update();

        $deviceBrandModel = $this->curdService->getModel();

        // 更新自定义品牌表
        $service->save($deviceBrandModel->model,$deviceBrandModel->brand);

        return $this->ret($ret, $deviceBrandModel);
    }

}
