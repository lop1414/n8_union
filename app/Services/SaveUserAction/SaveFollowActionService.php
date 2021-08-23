<?php

namespace App\Services\SaveUserAction;


use App\Enums\QueueEnums;
use App\Models\UserFollowActionModel;


class SaveFollowActionService extends SaveUserActionService
{

    protected $queueEnum = QueueEnums::USER_FOLLOW_ACTION;


    public function __construct(){
        parent::__construct();
        $model = new UserFollowActionModel();
        $this->setModel($model);
    }


    public function item($user,$data){
        // 关注行为比注册行为早
        if(empty($user)){
            $userData = array_merge([
                'n8_guid'    => $data['n8_guid'],
                'product_id' => $data['product_id'],
                'reg_time'   => $data['action_time'],
                'channel_id' => $data['channel_id'],
                'phone'      => $data['phone'] ?? '',
            ],$this->n8UnionUserService->filterDeviceInfo($data));
            $user = $this->userService->create($userData);
        }

        //union用户
        $unionUser = $this->n8UnionUserService->updateSave($user,$data);

        $createData = array_merge([
            'n8_guid'       => $data['n8_guid'],
            'uuid'          => $unionUser['id'],
            'product_id'    => $data['product_id'],
            'action_time'   => $data['action_time'],
            'channel_id'    => $data['channel_id'],
            'adv_alias'     => $data['adv_alias'],
            'created_at'    => date('Y-m-d H:i:s')
        ],$this->n8UnionUserService->filterDeviceInfo($data));
        $this->getModel()->create($createData);
        return $unionUser;

    }


}
