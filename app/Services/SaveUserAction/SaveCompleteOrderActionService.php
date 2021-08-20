<?php

namespace App\Services\UserActionDataToDb;


use App\Common\Enums\OrderStatusEnums;
use App\Common\Tools\CustomException;
use App\Enums\QueueEnums;
use App\Models\OrderModel;
use App\Services\GlobalOrderService;


class SaveCompleteOrderActionService extends SaveUserActionService
{

    protected $queueEnum = QueueEnums::USER_COMPLETE_ORDER_ACTION;


    public function __construct(){
        parent::__construct();
        $this->setModel(new OrderModel());
    }



    public function item($user,$data){

        $globalOrder = (new GlobalOrderService())->read($data['product_id'],$data['order_id']);

        $order = $this->getModel()->where('n8_goid',$globalOrder['n8_goid'])->first();

        if(empty($order)){
            throw new CustomException([
                'code'    => 'NOT_GOID',
                'message' => '找不到订单',
                'log'     => false,
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

        $order->complete_time = $data['action_time'];
        $order->status = OrderStatusEnums::COMPLETE;
        $order->complete_times = $completeTimes + 1;
        $order->save();
    }
}
