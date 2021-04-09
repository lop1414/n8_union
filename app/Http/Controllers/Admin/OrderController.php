<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\ConvertTypeEnum;
use App\Common\Services\SystemApi\AdvOceanApiService;
use App\Datas\N8GlobalOrderData;
use App\Models\OrderModel;

class OrderController extends BaseController
{

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

        $this->curdService->selectQueryAfter(function(){

            if(!empty($this->curdService->responseData['list'])){
                $convert = [];
                $payConvert = [];
                foreach ($this->curdService->responseData['list'] as $item){
                    array_push($convert,[
                        'convert_type' => ConvertTypeEnum::ORDER,
                        'convert_id'   => $item['n8_goid']
                    ]);

                    array_push($payConvert,[
                        'convert_type' => ConvertTypeEnum::PAY,
                        'convert_id'   => $item['n8_goid']
                    ]);
                }

                $tmp = (new AdvOceanApiService())->apiGetConvertCallbacks($convert);
                $convertList = array_column($tmp,null,'convert_id');

                $payTmp = (new AdvOceanApiService())->apiGetConvertCallbacks($payConvert);
                $payConvertList = array_column($payTmp,null,'convert_id');

                foreach ($this->curdService->responseData['list'] as $item){
                    $item->convert_callback = [
                        'order'            => $convertList[$item['n8_goid']]['convert_callback'],
                        'complete_order'   => $payConvertList[$item['n8_goid']]['convert_callback']
                    ];

                    $item->user;
                    $item->extend;
                }
            }


        });
    }


    public function readPrepare(){

        $this->curdService->findAfter(function(){
            $tmp = (new AdvOceanApiService())->apiGetConvertCallbacks([
                [
                    'convert_type' => ConvertTypeEnum::ORDER,
                    'convert_id'   => $this->curdService->responseData->n8_goid
                ]
            ]);

            $this->curdService->responseData->convert_callback = $tmp[0]['convert_callback'];

            $this->curdService->responseData->user;
            $this->curdService->responseData->extend;
        });
    }


}
