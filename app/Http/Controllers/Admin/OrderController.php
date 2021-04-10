<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\ConvertTypeEnum;
use App\Datas\N8GlobalOrderData;
use App\Models\OrderModel;

class OrderController extends UserActionBaseController
{


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
        $this->curdService->addField('product_id')->addValidRule('required');

        $this->curdService->selectQueryBefore(function(){
            $this->curdService->customBuilder(function ($builder){
                $builder->where('product_id',$this->curdService->requestData['product_id']);

                $orderId = $this->curdService->requestData['order_id'] ?? '';

                if(!empty($orderId)){
                    $globalOrder = (new N8GlobalOrderData())
                        ->setParams([
                            'product_id' => $this->curdService->requestData['product_id'],
                            'order_id'  => $orderId
                        ])
                        ->read();
                    if(!empty($globalOrder)){
                        $builder->where('n8_goid',$globalOrder['n8_goid']);

                    }
                }
            });
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
