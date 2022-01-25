<?php

namespace App\Services;

use App\Common\Services\BaseService;
use App\Datas\UserData;
use App\Models\UserExtendModel;
use App\Models\UserInfoChangeLogModel;
use App\Models\UserModel;

class UserService extends BaseService
{

    protected $userModelData;

    public function __construct(){
        parent::__construct();
        $this->model = new UserModel();
        $this->userModelData = new UserData();
    }


    public function read($n8Guid){
        return (new UserData())->setParams(['n8_guid'=>$n8Guid])->read();
    }


    public function create($data){

        $userInfo = $this->getModel()->create($data);
        $userInfo['extend'] = (new UserExtendModel())->create($data);
        return $userInfo;
    }




    public function update($n8Guid,$data){
        $user = $this->read($n8Guid);
        $changeLogData = $changeData = [];

        // 可变更字段
        $userAllowChangeField = ['channel_id', 'phone','reg_time'];

        foreach ($userAllowChangeField as $field){
            // 行为时间比当前用户注册时间早 则更新注册时间
            if($field == 'reg_time' && $user['reg_time'] > $data['action_time']){
                $changeData['reg_time'] = $data['action_time'];
            }elseif (isset($data[$field]) && $user[$field] != $data[$field]){
                $changeLogData[] = [
                    'n8_guid'       => $user['n8_guid'],
                    'field'         => $field,
                    'change_before' => $user[$field],
                    'change_after'  => $data[$field],
                    'change_time'   => $data['action_time']
                ];
                $changeData[$field] = $data[$field];
            }
        }

        if(!empty($changeData)){
            $changeData['updated_at'] = date('Y-m-d H:i:s');
            $user = $this->userModelData->update([
                'n8_guid' => $n8Guid
            ],$changeData);

            // 日志
            $this->saveChangeLog($changeLogData);
        }



        return $user;

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
