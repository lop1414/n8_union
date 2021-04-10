<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class UserActionModel extends BaseModel
{


    public function union_user($n8Guid,$channelId){
        return (new N8UnionUserModel())
            ->where('n8_guid',$n8Guid)
            ->where('channel_id',$channelId)
            ->first();
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
