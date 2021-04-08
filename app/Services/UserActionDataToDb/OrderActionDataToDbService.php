<?php

namespace App\Services\UserActionDataToDb;


use App\Common\Enums\OrderStatusEnums;
use App\Enums\QueueEnums;
use App\Models\OrderExtendModel;
use App\Models\OrderModel;
use App\Services\UnionUserService;


class OrderActionDataToDbService extends UserActionDataToDbService
{


    protected $queueEnum = QueueEnums::USER_ORDER_ACTION;


    public function __construct(){
        parent::__construct();
        $this->setModel(new OrderModel());
    }


    public function item($data,$globalUser){

        // 验证用户
        $user = $this->userIsExist($globalUser['n8_guid']);

        // 订单存在
        $globalOrder = $this->readGlobalOrder($data['product_id'],$data['order_id']);
        $this->orderIsNotExist($globalOrder['n8_goid']);

        // 创建union用户
        $unionUserService  = new UnionUserService();
        $unionUserService->setChannelIdByCpChannelId($data['product_id'],$data['cp_channel_id']);
        $unionUserService->setUser($user);
        $unionUserService->create($data);


        $this->getModel()->create([
            'n8_guid'       => $globalUser['n8_guid'],
            'n8_goid'       => $globalOrder['n8_goid'],
            'product_id'    => $data['product_id'],
            'channel_id'    => $unionUserService->getValidChannelId(),
            'order_time'    => $data['action_time'],
            'amount'        => $data['amount'],
            'type'          => $data['type'],
            'status'        => OrderStatusEnums::UN_PAID
        ]);

        $extendData = $unionUserService->filterDeviceInfo($data);
        $extendData['n8_goid'] = $globalOrder['n8_goid'];
        (new OrderExtendModel())->create($extendData);
    }
}
