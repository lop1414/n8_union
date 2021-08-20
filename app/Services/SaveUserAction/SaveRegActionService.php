<?php

namespace App\Services\UserActionDataToDb;


use App\Enums\QueueEnums;
use App\Models\UserModel;
use App\Services\N8UnionUserService;
use App\Services\UserService;


class SaveRegActionService extends SaveUserActionService
{


    public $userService;
    protected $queueEnum = QueueEnums::USER_REG_ACTION;

    public function __construct(){
        parent::__construct();
        $model = new UserModel();
        $this->setModel($model);
        $this->userService = new UserService();
    }



    public function item($user,$data){

        $n8UnionUserService  = new N8UnionUserService();

        if(empty($user)){
            // 创建用户
            $userData = array_merge([
                'n8_guid'    => $data['n8_guid'],
                'product_id' => $data['product_id'],
                'reg_time'   => $data['action_time'],
                'channel_id' => $data['channel_id'],
                'phone'      => $data['phone'] ?? '',
            ],$n8UnionUserService->filterDeviceInfo($data));
            $user = $this->userService->create($userData);
        }

        // 创建union用户
        $unionUser = $n8UnionUserService->updateSave($user,$data);

        if ($unionUser['channel_id'] != $user['channel_id']){
            $this->userService->update($data['n8_guid'],$data);
        }

    }


}
