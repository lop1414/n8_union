<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class UserModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * 关联到模型数据表的主键
     *
     * @var string
     */
    protected $primaryKey = 'n8_guid';


    /**
     * @var bool
     * 是否自增
     */
    public $incrementing = false;


    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'n8_guid',
        'product_id',
        'reg_time',
        'channel_id',
        'phone'
    ];



    /**
     * 关联用户扩展信息 一对一
     */
    public function extend(){
        return $this->hasOne('App\Models\UserExtendModel', 'n8_guid', 'n8_guid');
    }


    /**
     * 渠道信息 一对一
     */
    public function channel(){
        return $this->hasOne('App\Models\ChannelModel', 'id', 'channel_id');
    }

}
