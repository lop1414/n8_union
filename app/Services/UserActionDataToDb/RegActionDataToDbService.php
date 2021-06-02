<?php

namespace App\Services\UserActionDataToDb;


use App\Datas\N8UnionUserData;
use App\Enums\QueueEnums;
use App\Models\UserExtendModel;
use App\Models\UserModel;
use App\Services\UnionUserService;
use App\Services\UserService;


class RegActionDataToDbService extends UserActionDataToDbService
{


    protected $queueEnum = QueueEnums::USER_REG_ACTION;



    public function __construct(){
        parent::__construct();
        $model = new UserModel();
        $this->setModel($model);
    }




    public function item($data,$globalUser){

        $user = $this->readUser($globalUser['n8_guid']);

        // 用户存在
        if(!empty($user)){

            $this->changeUserItem($user,$data);
        }else{

            $this->saveUserItem($globalUser,$data);
        }

    }



    public function saveUserItem($globalUser,$data){

        $unionUserService  = new UnionUserService();
        $unionUserService->setChannelIdByCpChannelId($data['product_id'],$data['cp_channel_id']);
//        $unionUserService->closeVerify(); //关闭验证

        $saveData = [
            'n8_guid'    => $globalUser['n8_guid'],
            'product_id' => $globalUser['product_id'],
            'reg_time'   => $data['action_time'],
            'channel_id' => $unionUserService->getChannelId(),
            'phone'      => $data['phone'] ?? ''
        ];

        $userInfo = $this->getModel()->create($saveData);
        $extendData = $unionUserService->filterDeviceInfo($data);
        $extendData['n8_guid'] = $globalUser['n8_guid'];

        (new UserExtendModel())->create($extendData);



        // 创建union用户
        $unionUserService->setUser($saveData);
        $unionUserService->create($data);

        return $userInfo;
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
            // 修改 union_user 注册时间 兼容渠道变更用户 行为上报顺序问题
            if($unionUser['created_time'] > $data['action_time']){
                (new N8UnionUserData())
                    ->update([
                        'n8_guid' => $user['n8_guid']
                    ],[
                        'created_time'  => $data['action_time']
                    ]);
            }
        }

        return $userService->setUser($user)->update($data);
    }

}
