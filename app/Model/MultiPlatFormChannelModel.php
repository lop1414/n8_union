<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class MultiPlatFormChannelModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'multi_platform_channel';





    public function android_channel(){
        return $this->hasOne('App\Models\ChannelModel', 'id', 'android_channel_id');
    }


    public function ios_channel(){
        return $this->hasOne('App\Models\ChannelModel', 'id', 'ios_channel_id');
    }




}
