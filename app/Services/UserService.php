<?php

namespace App\Services;

use App\Common\Services\BaseService;
use App\Datas\UserData;
use App\Models\UserInfoChangeLogModel;
use App\Models\UserModel;

class UserService extends BaseService
{

    protected $user;


    protected $userModelData;

    public function __construct(){
        parent::__construct();
        $this->model = new UserModel();
        $this->userModelData = new UserData();
    }


    /**
     * 用户信息 可变更字段
     * @var string[]
     */
    protected $userAllowChangeField = array(
        'channel_id',
        'phone'
    );



    /**
     * @param $field
     * @return $this
     * 删除允许修改字段
     */
    public function delAllowChangeField($field){
        $this->userAllowChangeField = array_diff($this->userAllowChangeField,[$field]);
        return $this;
    }

    /**
     * @param $field
     * @return $this
     * 增加允许修改字段
     */
    public function addAllowChangeField($field){
        $this->userAllowChangeField = array_merge($this->userAllowChangeField,[$field]);
        return $this;
    }


    public function setUser($info){
        $this->user = $info;
        return $this;
    }



    public function update($data){
        $changeLogData = $changeData = [];

        foreach ($this->userAllowChangeField as $field){
            if(isset($data[$field]) && $this->user[$field] != $data[$field]){
                $changeLogData[] = [
                    'n8_guid'       => $this->user['n8_guid'],
                    'field'         => $field,
                    'change_before' => $this->user[$field],
                    'change_after'  => $data[$field],
                    'change_time'   => $data['action_time']
                ];
                $changeData[$field] = $data[$field];
            }
        }


        $userInfo = $this->userModelData->update([
            'n8_guid' => $this->user['n8_guid']
        ],$changeData);

        // 日志
        $this->saveChangeLog($changeLogData);

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
