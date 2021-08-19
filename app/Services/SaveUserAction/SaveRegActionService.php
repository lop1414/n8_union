<?php

namespace App\Services\UserActionDataToDb;


use App\Datas\N8UnionUserData;
use App\Enums\QueueEnums;
use App\Models\N8UnionUserExtendModel;
use App\Models\UserExtendModel;
use App\Models\UserModel;
use App\Services\UnionUserService;
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




    public function item($data){

        $user = $this->userService->read($data['n8_guid']);

        // 用户存在
        if(!empty($user)){

            $this->changeUserItem($user,$data);
        }else{

            $this->saveUserItem($data);
        }

    }

    public function changeUserItem($user,$data){

        $userService = new UserService();
        // 创建union用户

        $unionUserService  = new UnionUserService();
        $unionUserService->setChannelIdByCpChannelId($data['product_id'],$data['cp_channel_id']);
        $unionUserService->setUser($user);
        $unionUser = $unionUserService->create($data);
        if(!empty($unionUser)){
            // UnionUserService 中已更新channel_id了
            $userService->delAllowChangeField('channel_id');

            // 修改 union_user 注册时间、request_id  兼容渠道变更用户 行为上报顺序问题
            if($unionUser['created_time'] >= $data['action_time']){
                $unionUserModelData = new N8UnionUserData();
                $unionUserModelData->update([
                        'n8_guid'      => $unionUser['n8_guid'],
                        'channel_id'   => $unionUser['channel_id']
                    ],[
                        'created_time'  => $data['action_time']
                    ]);
                if(!empty($data['request_id'])){
                    $unionUser =  $unionUserModelData->setParams([
                            'n8_guid'      => $unionUser['n8_guid'],
                            'channel_id'   => $unionUser['channel_id']
                        ])->read();

                    (new N8UnionUserExtendModel())
                        ->where('uuid',$unionUser['id'])
                        ->update(['request_id'=>$data['request_id']]);
                }
            }
        }

        return $userService->setUser($user)->update($data);
    }

    public function saveUserItem($data){

        $unionUserService  = new UnionUserService();
        $unionUserService->setChannelIdByCpChannelId($data['product_id'],$data['cp_channel_id']);
//        $unionUserService->closeVerify(); //关闭验证


        // 创建用户
        $userData = array_merge([
            'n8_guid'    => $data['n8_guid'],
            'product_id' => $data['product_id'],
            'reg_time'   => $data['action_time'],
            'channel_id' => $unionUserService->getChannelId(),
            'phone'      => $data['phone'] ?? '',
        ],$unionUserService->filterDeviceInfo($data));
        $this->userService->create($userData);


        // 创建union用户
        $unionUserService->setUser($saveData);
        $unionUserService->create($data);

        return $userInfo;
    }

}
