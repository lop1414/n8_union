<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class UserFollowActionModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user_follow_actions';

    /**
     * 禁用默认更新时间
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'n8_guid',
        'action_time',
        'channel_id',
        'ip',
        'ua',
        'request_id',
        'created_at'
    ];


}
