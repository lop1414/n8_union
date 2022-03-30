<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class ChannelExtendModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'channel_extends';


    protected $primaryKey = 'channel_id';


    /**
     * @var bool
     * 不自增
     */
    public $incrementing = false;


    protected $fillable = [
        'channel_id',
        'adv_alias',
        'status',
        'admin_id',
        'parent_id'
    ];



    public function channel(){
        return $this->hasOne('App\Models\ChannelModel', 'id', 'channel_id');
    }


}
