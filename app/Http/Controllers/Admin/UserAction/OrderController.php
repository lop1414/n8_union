<?php


namespace App\Http\Controllers\Admin\UserAction;


use App\Common\Enums\ConvertTypeEnum;

use App\Datas\N8GlobalOrderData;
use App\Models\OrderModel;

class OrderController extends UserActionBaseController
{


    /**
     * @var string
     * 默认排序字段
     */
    protected $defaultOrderBy = 'order_time';


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

        $this->selectFilterOpenId();
        $this->selectFilter();
        $this->selectFilterAdv(ConvertTypeEnum::PAY,'n8_goid','complete_click_id');
        //书城 order_id 过滤
        $this->curdService->selectQueryBefore(function(){
            $this->curdService->customBuilder(function ($builder){
                $orderId = $this->curdService->requestData['order_id'] ?? '';
                if(!empty($orderId)){
                    $globalOrder = (new N8GlobalOrderData())
                        ->setParams([
                            'product_id' => $this->curdService->requestData['product_id'],
                            'order_id'  => $orderId
                        ])
                        ->read();
                    if(!empty($globalOrder)) $globalOrder['n8_goid'] = 0;

                    $builder->where('n8_goid',$globalOrder['n8_goid']);
                }
            });
        });


        $this->selectConvertMap(ConvertTypeEnum::ORDER,'n8_goid');
        $this->selectConvertMap(ConvertTypeEnum::PAY,'n8_goid');

        $this->curdService->selectQueryAfter(function() {
            foreach ($this->curdService->responseData['list'] as $item){
                $item->user;
                $item->global_user;
                $item->channel;
                $item->union_user;
                $item->union_user->channel;
                $item->admin_name = $this->adminUserService->readName($item->union_user->admin_id);
                $item->global_order;
                $item->extend;
            }
        });

    }



    public function itemConvertCallBack($convertType,$convertCallback): array
    {
        $key = strtolower($convertType);
        return [$key => $convertCallback];
    }


}
