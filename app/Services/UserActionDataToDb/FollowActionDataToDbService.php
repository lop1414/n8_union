<?php

namespace App\Services\UserActionDataToDb;


use App\Enums\QueueEnums;
use App\Models\UserFollowActionModel;
use App\Services\UnionUserService;


class FollowActionDataToDbService extends UserActionDataToDbService
{

    protected $queueEnum = QueueEnums::USER_FOLLOW_ACTION;


    public function __construct(){
        parent::__construct();
        $model = new UserFollowActionModel();
        $this->setModel($model);
    }




    public function item($data,$globalUser){

        // 验证用户
        $user = $this->userIsExist($globalUser['n8_guid']);

        // 创建union用户
        $unionUserService  = new UnionUserService();
        $unionUserService->setChannelIdByCpChannelId($data['product_id'],$data['cp_channel_id']);
        $unionUserService->setUser($user);
        $unionUser = $unionUserService->create($data);


        $deviceData = $unionUserService->filterDeviceInfo($data);
        $channelId = $unionUserService->getValidChannelId();
        $advAlias = $this->getAdvAliasByChannel($channelId);
        $createData = array_merge($deviceData,[
            'n8_guid'       => $globalUser['n8_guid'],
            'uuid'          => $unionUser['id'],
            'product_id'    => $globalUser['product_id'],
            'action_time'   => $data['action_time'],
            'channel_id'    => $channelId,
            'adv_alias'     => $advAlias,
            'created_at'    => date('Y-m-d H:i:s')
        ]);
        $this->getModel()->create($createData);
    }



}
