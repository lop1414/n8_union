<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class UserActionBaseModel extends BaseModel
{


    public function union_user(){
        return $this->hasOne('App\Models\N8UnionUserModel', 'uuid', 'id');
    }


    public function global_user(){
        return $this->hasOne('App\Models\N8GlobalUserModel', 'n8_guid', 'n8_guid');
    }



    /**
     * 关联用户信息 一对一
     */
    public function user(){
        return $this->hasOne('App\Models\UserModel', 'n8_guid', 'n8_guid');
    }


    /**
     * 关联用户扩展信息 一对一
     */
    public function user_extend(){
        return $this->hasOne('App\Models\UserExtendModel', 'n8_guid', 'n8_guid');
    }



    /**
     * 渠道信息 一对一
     */
    public function channel(){
        return $this->hasOne('App\Models\ChannelModel', 'id', 'channel_id');
    }
}
