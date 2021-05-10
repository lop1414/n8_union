<?php

namespace App\Services\UserActionDataToDb;


use App\Common\Enums\OrderStatusEnums;
use App\Common\Tools\CustomException;
use App\Datas\N8GlobalOrderData;
use App\Enums\QueueEnums;
use App\Models\OrderModel;


class CompleteOrderActionDataToDbService extends UserActionDataToDbService
{

    protected $queueEnum = QueueEnums::USER_COMPLETE_ORDER_ACTION;


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

        $completeTimes = (new OrderModel())
            ->where('n8_guid',$order['n8_guid'])
            ->where('channel_id',$order['channel_id'])
            ->where('status',OrderStatusEnums::COMPLETE)
            ->where('order_time','<',$order['order_time'])
            ->count();

        $order->complete_time = $data['complete_time'];
        $order->status = OrderStatusEnums::COMPLETE;
        $order->complete_times = $completeTimes + 1;
        $order->save();
    }
}
