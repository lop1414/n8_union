<?php

namespace App\Services\UserActionDataToDb;


use App\Enums\QueueEnums;
use App\Models\UserLoginActionModel;


class LoginActionService extends UserActionDataToDbService
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

        $channelId = $this->readChannelId($data['product_id'],$data['cp_channel_id']);
        $this->createUnionUser($user,$channelId,$data);

        $deviceData = $this->filterDeviceInfo($data);
        $createData = array_merge($deviceData,[
            'n8_guid'       => $user['n8_guid'],
            'action_time'   => $data['action_time'],
            'channel_id'    => $user['channel_id'],
            'created_at'    => date('Y-m-d H:i:s')
        ]);
        $this->getModel()->create($createData);
    }



}
