<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\StatusEnum;
use App\Common\Tools\CustomException;
use App\Common\Enums\CpTypeEnums;
use App\Datas\ProductData;
use App\Models\CpAccountModel;
use App\Services\SyncProductService;
use Illuminate\Http\Request;

class CpAccountController extends BaseController
{

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new CpAccountModel();

        parent::__construct();
    }

    /**
     * 保持验证规则
     */
    public function saveValidRule(){
        $this->curdService->addField('account')->addValidRule('required');
        $this->curdService->addField('cp_type')->addValidRule('required')
            ->addValidEnum(CpTypeEnums::class);
        $this->curdService->addField('status')->addValidEnum(StatusEnum::class)
            ->addDefaultValue(StatusEnum::ENABLE);
    }

    /**
     * 创建预处理
     */
    public function createPrepare(){
        $this->saveValidRule();

        $this->curdService->saveBefore(function(){


            if($this->curdService->getModel()->uniqueExist([
                'cp_type' => $this->curdService->handleData['cp_type'],
                'account' => $this->curdService->handleData['account']
            ])){
                throw new CustomException([
                    'code' => 'ACCOUNT_EXIST',
                    'message' => '账户已存在'
                ]);
            }
        });
    }

    /**
     * 更新预处理
     */
    public function updatePrepare(){
        $this->saveValidRule();

        $this->curdService->saveBefore(function(){

            if(
                (
                    $this->curdService->getModel()->cp_type != $this->curdService->handleData['cp_type']
                    || $this->curdService->getModel()->account != $this->curdService->handleData['account']
                )
                && $this->curdService->getModel()->uniqueExist([
                'cp_type' => $this->curdService->handleData['cp_type'],
                'account' => $this->curdService->handleData['account']
            ])){
                throw new CustomException([
                    'code' => 'ACCOUNT_EXIST',
                    'message' => '账户已存在'
                ]);
            }

        });
    }


    public function syncProduct(Request $request){
        $requestData = $request->all();

        $this->curdService->setRequestData($requestData);

        // 查找
        $item = $this->curdService->read();


        if($item->cp_type == CpTypeEnums::YW){
            $service = new SyncProductService();
            $service->h5($item);
            $service->kyy($item);
        }

        // 清除所有产品缓存
        (new ProductData())->clearAll();

        return $this->success();

    }
}
