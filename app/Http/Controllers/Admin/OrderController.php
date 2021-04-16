<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\ConvertTypeEnum;
use App\Datas\N8GlobalOrderData;
use App\Datas\N8GlobalUserData;
use App\Models\OrderModel;

class OrderController extends UserActionBaseController
{


    /**
     * @var string
     * 默认排序字段
     */
    protected $defaultOrderBy = 'order_time';


    public $isConvertCallbackKey = true;

    public $convertId = 'n8_goid';


    /**
     * constructor.
     */
    public function __construct()
    {
        $this->model = new OrderModel();

        parent::__construct();
    }



    /**
     * 分页列表预处理
     */
    public function selectPrepare(){

        $this->selectUserCommonFilter(function($builder){
            $openId = $this->curdService->requestData['open_id'] ?? '';
            if(!empty($openId)){
                $globalUser = (new N8GlobalUserData())
                    ->setParams([
                        'product_id' => $this->curdService->requestData['product_id'],
                        'open_id'  => $openId
                    ])
                    ->read();
                if(!empty($globalUser)){
                    $builder->where('n8_guid',$globalUser['n8_guid']);

                }
            }
        });


        $this->selectCommonPrepare(ConvertTypeEnum::ORDER);
        $this->selectCommonPrepare(ConvertTypeEnum::PAY);

    }


    public function itemPrepare($item){
        $item->global_order;
        $item->extend;
    }




    public function readPrepare(){
        $this->readCommonPrepare(ConvertTypeEnum::ORDER);
        $this->readCommonPrepare(ConvertTypeEnum::PAY);

        $this->curdService->findAfter(function(){
            $this->curdService->responseData->global_order;
            $this->curdService->responseData->extend;
        });
    }


}
