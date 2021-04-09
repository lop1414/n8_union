<?php

namespace App\Services\UserActionDataToDb;


use App\Common\Enums\OrderStatusEnums;
use App\Common\Tools\CustomException;
use App\Datas\N8GlobalOrderData;
use App\Enums\QueueEnums;
use App\Models\OrderModel;


class CompleteOrderActionDataToDbService extends UserActionDataToDbService
{

    protected $queueEnum = QueueEnums::COMPLETE_ORDER;


    public function __construct(){
        parent::__construct();
        $this->setModel(new OrderModel());
    }


    public function item($data,$globalUser){

        $globalOrder = (new N8GlobalOrderData())
            ->setParams(['product_id' => $data['product_id'], 'order_id' => $data['order_id']])
            ->read();

        $order = $this->getModel()->where('n8_goid',$globalOrder['n8_goid'])->first();

        if(empty($order)){
            throw new CustomException([
                'code'    => 'NOT_GUID',
                'message' => '找不到订单',
                'log'     => true,
                'data'    => [
                    'n8_goid' => $globalOrder['n8_goid']
                ]
            ]);
        }

        $order->complete_time = $data['complete_time'];
        $order->status = OrderStatusEnums::COMPLETE;
        $order->save();
    }
}
