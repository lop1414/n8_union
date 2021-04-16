<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\MatcherEnum;
use App\Common\Enums\StatusEnum;
use App\Common\Tools\CustomException;
use App\Common\Enums\CpTypeEnums;
use App\Datas\ProductData;
use App\Models\CpAccountModel;
use App\Models\ProductModel;
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

        $list = [];
        if($item->cp_type == CpTypeEnums::YW){
            $service = new SyncProductService();
            $list = array_merge($list,$service->h5($item));
            $list = array_merge($list,$service->kyy($item));
        }

        // 保存
        foreach($list as $item){
            $pro = (new ProductModel())
                ->where('cp_account_id',$item['cp_account_id'])
                ->where('cp_product_alias',$item['cp_product_alias'])
                ->where('type',$item['type'])
                ->first();

            if(empty($pro)){
                $pro = new ProductModel();
                $pro->cp_account_id = $item['cp_account_id'];
                $pro->cp_product_alias = $item['cp_product_alias'];
                $pro->cp_type = $item['cp_type'];
                $pro->type = $item['type'];
                $pro->secret = md5(uniqid());
                $pro->status = StatusEnum::DISABLE;
                $pro->matcher = MatcherEnum::SYS;
            }
            $pro->name = $item['name'];
            $pro->save();
        }

        // 清除所有产品缓存
        (new ProductData())->clearAll();

        return $this->success();

    }
}
