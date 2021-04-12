<?php

namespace App\Services\UserActionDataToDb;


use App\Enums\QueueEnums;
use App\Models\UserLoginActionModel;
use App\Services\UnionUserService;


class LoginActionDataToDbService extends UserActionDataToDbService
{

    protected $queueEnum = QueueEnums::USER_LOGIN_ACTION;


    public function __construct(){
        parent::__construct();
        $model = new UserLoginActionModel();
        $this->setModel($model);
    }



    public function item($data,$globalUser){

        // 验证用户
        $user = $this->userIsExist($globalUser['n8_guid']);

        // 创建union用户
        $unionUserService  = new UnionUserService();
        $unionUserService->setChannelIdByCpChannelId($data['product_id'],$data['cp_channel_id']);
        $unionUserService->setUser($user);
        $unionUserService->create($data);


        $deviceData = $unionUserService->filterDeviceInfo($data);
        $channelId = $unionUserService->getValidChannelId();
        $advAlias = $this->getAdvAliasByChannel($channelId);
        $createData = array_merge($deviceData,[
            'n8_guid'       => $globalUser['n8_guid'],
            'product_id'    => $globalUser['product_id'],
            'action_time'   => $data['action_time'],
            'channel_id'    => $channelId,
            'adv_alias'     => $advAlias,
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        $this->getModel()->setTableNameWithMonth($createData['action_time'])->create($createData);

    }



}
