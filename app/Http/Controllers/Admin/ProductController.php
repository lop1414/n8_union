<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\MatcherEnum;
use App\Common\Enums\OperatorEnum;
use App\Common\Enums\StatusEnum;
use App\Common\Enums\SystemAliasEnum;
use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Datas\ProductData;
use App\Models\ProductAdminModel;
use App\Models\ProductModel;
use App\Models\ProductMoneyDivideLogModel;
use App\Models\ProductMoneyDivideModel;
use App\Services\ProductAdminService;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    /**
     * @var string
     * 默认排序字段
     */
    protected $defaultOrderBy = 'id';

    /**
     * @var string
     * 默认排序类型
     */
    protected $defaultOrderType = 'asc';

    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new ProductModel();
        $this->modelData = new ProductData();

        parent::__construct();
    }


    /**
     * 过滤
     */
    public function dataFilter(){
        $this->curdService->customBuilder(function ($builder){
            $req = $this->curdService->requestData;
            if(!empty($req['is_self'])){
                // 非管理员
                if(!$this->adminUserService->isAdmin()) {
                    $adminIds = implode(',',$this->adminUserService->getChildrenAdminIds());
                    $status = StatusEnum::ENABLE;
                    $builder->whereRaw("id IN (SELECT product_id FROM product_admins WHERE status = '{$status}' AND (admin_id = 0 OR admin_id IN ({$adminIds})))");
                }
            }
        });
    }



    /**
     * 分页列表预处理
     */
    public function selectPrepare(){
        $this->curdService->selectQueryBefore(function (){
            $this->dataFilter();
        });

        $this->curdService->selectQueryAfter(function(){

            $n8UnionUrl = config('common.system_api.'.SystemAliasEnum::UNION.'.url');
            $n8TransferDataUrl = config('common.system_api.'.SystemAliasEnum::TRANSFER.'.data_url');
            foreach ($this->curdService->responseData['list'] as $item){
                $item->cp_account;

                $admins = [];
                $item->is_public = 0;
                foreach ($item->product_admin as $productAdmin){
                    if($productAdmin['admin_id'] == 0){
                        $item->is_public = 1;
                        continue;
                    }
                    array_push($admins,$this->adminUserService->read($productAdmin['admin_id']));
                }
                $item->admins = $admins;

                $copyUrl = [];

                if($item->cp_type == CpTypeEnums::YW){
                    $copyUrl[] = [
                        'name' => '阅读数据接收地址',
                        'url'  => $n8UnionUrl.'/open/yw/action_report/read'
                    ];

                    if($item->type == ProductTypeEnums::KYY){
                        $copyUrl[] = [
                            'name' => '用户数据接收地址',
                            'url'  => $n8TransferDataUrl.'/open/yw_kyy/user'
                        ];
                    }

                    if ($item->type == ProductTypeEnums::H5){
                        $copyUrl[] = [
                            'name' => '用户数据接收地址',
                            'url'  => $n8TransferDataUrl.'/open/yw_h5/user'
                        ];
                    }
                }
                $item->copy_url = $copyUrl;
            }
        });
    }



    /**
     * 列表预处理
     */
    public function getPrepare(){
        $this->curdService->getQueryBefore(function (){
            $this->dataFilter();
        });

        $this->curdService->getQueryAfter(function(){
            foreach ($this->curdService->responseData as $item){
                $item->cp_account;
            }
        });
    }




    /**
     * 详情预处理
     */
    public function readPrepare(){
        $this->curdService->findAfter(function(){
            $this->curdService->responseData;

            $this->curdService->responseData->cp_account;

            $admins = [];
            $this->curdService->responseData->is_public = 0;
            foreach ($this->curdService->responseData->product_admin as $item){
                if($item['admin_id'] == 0){
                    $this->curdService->responseData->is_public = 1;
                    continue;
                }
                array_push($admins,$this->adminUserService->read($item['admin_id']));
            }
            $this->curdService->responseData->admins = $admins;
        });
    }



    /**
     * 保存验证规则
     */
    public function saveValidRule(){
        $this->curdService->addField('name')->addValidRule('required|max:12');
        $this->curdService->addField('cp_type')->addValidRule('required')
            ->addValidEnum(CpTypeEnums::class);
        $this->curdService->addField('type')->addValidRule('required')
            ->addValidEnum(ProductTypeEnums::class);
        $this->curdService->addField('cp_product_alias')->addValidRule('required');
        $this->curdService->addField('cp_account_id')->addDefaultValue(0);
        $this->curdService->addField('status')
            ->addValidEnum(StatusEnum::class)
            ->addDefaultValue(StatusEnum::ENABLE);
        $this->curdService->addField('matcher')
            ->addValidEnum(MatcherEnum::class)
            ->addDefaultValue(MatcherEnum::SYS);
        $this->curdService->addField('operator')
            ->addValidEnum(OperatorEnum::class)
            ->addDefaultValue(OperatorEnum::SYS);
        $this->curdService->addField('cp_account_id')
            ->addValidRule('integer')
            ->addDefaultValue(0);
    }


    /**
     * 创建预处理
     */
    public function createPrepare(){
        $this->saveValidRule();

        $this->curdService->saveBefore(function(){


            $this->curdService->handleData['secret'] = Functions::makeSecret();

            if($this->curdService->getModel()->uniqueExist([
                'cp_type' => $this->curdService->handleData['cp_type'],
                'cp_product_alias' => $this->curdService->handleData['cp_product_alias']
            ])){
                throw new CustomException([
                    'code' => 'PRODUCT_EXIST',
                    'message' => '产品已存在'
                ]);
            }
        });

        $this->curdService->saveAfter(function (){
            // 创建产品所有者记录
            (new ProductAdminService())->update([
                'admin_id' => 0,
                'product_id' => $this->curdService->getModel()->id,
                'status' => StatusEnum::ENABLE
            ]);
        });
    }

    /**
     * 更新预处理
     */
    public function updatePrepare(){
        $this->saveValidRule();

        $this->curdService->saveBefore(function(){

            if(
                $this->curdService->getModel()->name != $this->curdService->handleData['name']
                && $this->curdService->getModel()->exist('name', $this->curdService->handleData['name'])
            ){
                throw new CustomException([
                    'code' => 'PRODUCT_NAME_EXIST',
                    'message' => '产品名称已存在'
                ]);
            }

            if(
                (
                    $this->curdService->getModel()->cp_type != $this->curdService->handleData['cp_type']
                    || $this->curdService->getModel()->cp_product_alias != $this->curdService->handleData['cp_product_alias']
                )
                && $this->curdService->getModel()->uniqueExist([
                'cp_type' => $this->curdService->handleData['cp_type'],
                'cp_product_alias' => $this->curdService->handleData['cp_product_alias']
            ])){
                throw new CustomException([
                    'code' => 'PRODUCT_EXIST',
                    'message' => '产品已存在'
                ]);
            }

            unset($this->curdService->handleData['secret']);
        });
    }



    /**
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * 分配管理员
     */
    public function distribution(Request $request){
        $requestData = $request->all();
        $this->validRule($requestData,[
            'id' => 'required',
            'is_public' => 'required'
        ],[
            'id.required' => 'id 不能为空',
            'is_public.required' => 'is_public 不能为空'
        ]);


        $productAdminService = new ProductAdminService();
        $list = (new ProductAdminModel())
            ->where('product_id', $requestData['id'])
            ->get();

        if($requestData['is_public'] == 1){
            foreach ($list as $item){
                $productAdminService->update([
                    'product_id' => $requestData['id'],
                    'admin_id' => $item['admin_id'],
                    'status'    => StatusEnum::DISABLE
                ]);
            }

            $productAdminService->update([
                'product_id' =>  $requestData['id'],
                'admin_id' => '0',
                'status' => StatusEnum::ENABLE,
            ]);
        }else {
            if(empty($requestData['admin_ids'])){
                throw new CustomException([
                    'code' => 'UNVALID',
                    'message' => 'admin_ids 不能为空',
                ]);
            }
            // 获取需禁用的记录
            $disableAdminIds = $list->isEmpty()
                ? []
                : array_diff(array_column($list->toArray(), 'admin_id'), $requestData['admin_ids']);

            $productAdminService->batchUpdate([
                'product_ids' => [$requestData['id']],
                'admin_ids' => $disableAdminIds,
                'status' => StatusEnum::DISABLE
            ]);

            $productAdminService->batchUpdate([
                'product_ids' => [$requestData['id']],
                'admin_ids' => $requestData['admin_ids'],
                'status' => StatusEnum::ENABLE,
            ]);
        }
        return $this->success();
    }



    /**
     * @param Request $request
     * @return bool
     * @throws CustomException
     * 充值分成
     */
    public function moneyDivide(Request $request)
    {
        $requestData = $request->all();
        $this->validRule($requestData,[
            'id' => 'required',
            'divide' => 'required|int'
        ],[
            'id.required' => 'id 不能为空',
            'divide.required' => 'divide 不能为空',
            'divide.int' => 'divide 不是int类型',
        ]);


        $productMoneyDivideModel = new ProductMoneyDivideModel();
        $productMoneyDivide = $productMoneyDivideModel->where('product_id', $requestData['id'])->first();


        if(empty($productMoneyDivide)){
            $productMoneyDivide = new ProductMoneyDivideModel();
        }

        $productMoneyDivide->product_id = $requestData['id'];
        $productMoneyDivide->divide = $requestData['divide'];
        $ret = $productMoneyDivide->save();

        // 日志表
        if($ret && !empty($productMoneyDivide->product_id)){
            $productMoneyDivideLog = new ProductMoneyDivideLogModel();
            $productMoneyDivideLog->product_id = $requestData['id'];
            $productMoneyDivideLog->divide = $requestData['divide'];
            $productMoneyDivideLog->save();
        }

        return $this->success();
    }
}
