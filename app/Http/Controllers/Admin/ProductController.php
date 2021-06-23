<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\MatcherEnum;
use App\Common\Enums\OperatorEnum;
use App\Common\Enums\StatusEnum;
use App\Common\Helpers\Functions;
use App\Common\Tools\CustomException;
use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Datas\ProductData;
use App\Models\ProductModel;

class ProductController extends BaseController
{
    /**
     * @var string
     * 默认排序字段
     */
    protected $defaultOrderBy = 'id';

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
     * 分页列表预处理
     */
    public function selectPrepare(){


        $this->curdService->selectQueryAfter(function(){

            foreach ($this->curdService->responseData['list'] as $item){
                $item->cp_account;
            }
        });
    }



    /**
     * 列表预处理
     */
    public function getPrepare(){


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

            if($this->curdService->getModel()->exist('name', $this->curdService->handleData['name'])){
                throw new CustomException([
                    'code' => 'PRODUCT_NAME_EXIST',
                    'message' => '产品名称已存在'
                ]);
            }
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
}
