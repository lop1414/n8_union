<?php


namespace App\Http\Controllers\Admin;


use App\Common\Enums\ConvertTypeEnum;
use App\Common\Services\SystemApi\AdvOceanApiService;
use App\Datas\N8GlobalOrderData;
use App\Models\OrderModel;
use App\Services\ConvertCallbackMapService;

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

                $convertList = (new ConvertCallbackMapService())
                    ->listMap($this->curdService->responseData['list'],ConvertTypeEnum::ORDER,'n8_goid');
                $payConvertList = (new ConvertCallbackMapService())
                    ->listMap($this->curdService->responseData['list'],ConvertTypeEnum::PAY,'n8_goid');

                foreach ($this->curdService->responseData['list'] as $item){
                    $item->convert_callback = [
                        'order'            => $convertList[$item['n8_goid']]['convert_callback'],
                        'complete_order'   => $payConvertList[$item['n8_goid']]['convert_callback']
                    ];

                    $item->user;
                    $item->global_user;
                    $item->global_order;
                    $item->union_user = $this->model->union_user($item->n8_guid,$item->channel_id);
                    $item->channel;
                    $item->extend;
                }
            }


        });
    }


    public function readPrepare(){

        $this->curdService->findAfter(function(){

            $n8Goid = $this->curdService->responseData->n8_goid;

            $convertList = (new ConvertCallbackMapService())
                ->listMap([$this->curdService->responseData],ConvertTypeEnum::ORDER,'n8_goid');
            $payConvertList = (new ConvertCallbackMapService())
                ->listMap([$this->curdService->responseData],ConvertTypeEnum::PAY,'n8_goid');

            $this->curdService->responseData->convert_callback = [
                'order'            => $convertList[$n8Goid]['convert_callback'],
                'complete_order'   => $payConvertList[$n8Goid]['convert_callback']
            ];

            $this->curdService->responseData->user;
            $this->curdService->responseData->global_user;
            $this->curdService->responseData->global_order;
            $this->curdService->responseData->union_user = $this->model->union_user($this->curdService->responseData->n8_guid,$this->curdService->responseData->channel_id);

            $this->curdService->responseData->channel;
            $this->curdService->responseData->extend;
        });
    }


}
