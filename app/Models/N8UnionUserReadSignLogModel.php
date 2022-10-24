<?php

namespace App\Models;


class N8UnionUserReadSignLogModel extends UserActionBaseModel
{
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
    protected $table = 'n8_union_user_read_sign_log';
    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'read_sign_type',
        'created_time'
    ];

}
