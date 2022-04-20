<?php

namespace App\Services\SaveUserAction;


use App\Common\Enums\OrderStatusEnums;
use App\Common\Tools\CustomException;
use App\Datas\OrderData;
use App\Enums\QueueEnums;
use App\Models\OrderExtendModel;
use App\Models\OrderModel;
use App\Services\GlobalOrderService;


class SaveOrderActionService extends SaveUserActionService
{


    protected $queueEnum = QueueEnums::USER_ORDER_ACTION;


    public function __construct(){
        parent::__construct();
        $this->setModel(new OrderModel());
    }



    public function item($user,$data){

        // 订单存在
        $globalOrder = (new GlobalOrderService())->make($data['product_id'],$data['order_id']);
        $order = (new OrderData())->setParams(['n8_goid'=>$globalOrder['n8_goid']])->read();
        if(!empty($order)){
            throw new CustomException([
                'code'    => 'EXIST_ORDER',
                'message' => '订单已存在',
                'log'     => false,
                'data'    => [
                    'n8_goid' => $globalOrder['n8_goid']
                ]
            ]);
        }


        //union用户
        $unionUser = $this->n8UnionUserService->updateSave($user,$data);


        // 入库
        $tmpTime = date('Y-m-d H:i:s',strtotime('-24 hours',strtotime($data['action_time'])));
        $orderTimes = (new OrderModel())
            ->whereRaw("
                n8_guid = {$data['n8_guid']}
                AND (
                    channel_id = {$unionUser['channel_id']}
                    OR ( channel_id = 0 AND order_time BETWEEN '{$tmpTime}' AND '{$data['action_time']}')
                )
            ")
            ->count();
        $status = OrderStatusEnums::COMPLETE;
        $completeTimes = (new OrderModel())
            ->whereRaw("
                n8_guid = {$data['n8_guid']}
                AND status = '{$status}'
                AND (
                    channel_id = {$unionUser['channel_id']}
                    OR ( channel_id = 0 AND order_time BETWEEN '{$tmpTime}' AND '{$data['action_time']}')
                )
            ")
            ->count();


        $this->getModel()->create([
            'n8_guid'       => $data['n8_guid'],
            'n8_goid'       => $globalOrder['n8_goid'],
            'uuid'          => $unionUser['id'],
            'product_id'    => $data['product_id'],
            'channel_id'    => $data['channel_id'],
            'adv_alias'     => $data['adv_alias'],
            'order_time'    => $data['action_time'],
            'amount'        => $data['amount'],
            'type'          => $data['type'],
            'status'        => OrderStatusEnums::UN_PAID,
            'order_times'   => $orderTimes + 1,
            'complete_times'=> $completeTimes
        ]);

        $extendData = array_merge([
            'n8_goid' => $globalOrder['n8_goid']
        ],$this->n8UnionUserService->filterDeviceInfo($data));

        (new OrderExtendModel())->create($extendData);

        return $unionUser;
    }

}
