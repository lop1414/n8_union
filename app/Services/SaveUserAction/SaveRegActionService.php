<?php

namespace App\Services\SaveUserAction;


use App\Enums\QueueEnums;
use App\Models\UserModel;
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


        if(empty($user)){
            // 创建用户
            $userData = array_merge([
                'n8_guid'    => $data['n8_guid'],
                'product_id' => $data['product_id'],
                'reg_time'   => $data['action_time'],
                'channel_id' => $data['channel_id'],
                'phone'      => $data['phone'] ?? '',
            ],$this->n8UnionUserService->filterDeviceInfo($data));
            $user = $this->userService->create($userData);
        }

        // 创建union用户
        return $this->n8UnionUserService->updateSave($user,$data);
    }


}
