<?php

namespace App\Services\UserActionDataToDb;


use App\Enums\QueueEnums;
use App\Models\UserExtendModel;
use App\Models\UserInfoChangeLogModel;
use App\Models\UserModel;
use App\Services\UnionUserService;


class RegActionDataToDbService extends UserActionDataToDbService
{


    protected $queueEnum = QueueEnums::USER_REG_ACTION;


    /**
     * 用户信息 可变更字段
     * @var string[]
     */
    protected $userAllowChangeField = array(
        'channel_id',
        'phone'
    );



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


        if(!empty($data['cp_channel_id'])){

            // 创建union用户
            $unionUserService  = new UnionUserService();
            $unionUserService->setChannelIdByCpChannelId($data['product_id'],$data['cp_channel_id']);
            $unionUserService->closeVerify(); //关闭验证
            $unionUserService->setUser($saveData);
            $unionUserService->create($data);
        }


        return $userInfo;
    }





    public function changeUserItem($user,$data,$createUnionUser = true){

        $changeLogData = $changeData = [];
        $n8Guid = $user['n8_guid'];

        // 创建union用户
        if($createUnionUser){
            $unionUserService  = new UnionUserService();
            $unionUserService->setChannelIdByCpChannelId($data['product_id'],$data['cp_channel_id']);
            $unionUserService->setUser($user);
            $unionUser = $unionUserService->create($data);
            if(!empty($unionUser)){
                $data['channel_id'] = $unionUser['channel_id'];
            }
        }



        foreach ($this->userAllowChangeField as $field){
            if(isset($data[$field]) && $user[$field] != $data[$field]){
                $changeLogData[] = [
                    'n8_guid'       => $n8Guid,
                    'field'         => $field,
                    'change_before' => $user[$field],
                    'change_after'  => $data[$field],
                    'change_time'   => $data['action_time']
                ];
                $changeData[$field] = $data[$field];
            }
        }
        $userInfo = $this->getModel()->where('n8_guid',$n8Guid)->update($changeData);


        // 日志
        $this->saveChangeLog($changeLogData);

        // 更新缓存
        $this->refreshUserData($n8Guid);

        return $userInfo;
    }




    /**
     * @param $data
     * 保存更改日志
     */
    public function saveChangeLog($data){

        foreach ($data as $item){
            $model = new UserInfoChangeLogModel();
            $model->n8_guid         = $item['n8_guid'];
            $model->field           = $item['field'];
            $model->change_before   = $item['change_before'];
            $model->change_after    = $item['change_after'];
            $model->change_time     = $item['change_time'];
            $model->created_at      = date('Y-m-d H:i:s');
            $model->save();
        }
    }
}
