<?php

namespace App\Models;


class UserFollowActionModel extends UserActionBaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user_follow_actions';

    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'n8_guid',
        'uuid',
        'product_id',
        'action_time',
        'adv_alias',
        'click_id',
        'channel_id',
        'ip',
        'ua',
        'request_id',
        'click_id',
        'last_match_time',
        'created_at'
    ];


    /**
     * 禁用默认更新时间
     *
     * @var bool
     */
    public $timestamps = false;


}
