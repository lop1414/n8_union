<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class ChannelSupportModel extends BaseModel
{
    /**
     * @var bool
     * 不自增
     */
    public $incrementing = false;
    /**
     * 禁用默认更新时间
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'channel_supports';
    protected $primaryKey = 'channel_id';

    public function channel(){
        return $this->hasOne('App\Models\ChannelModel', 'id', 'channel_id');
    }


}
