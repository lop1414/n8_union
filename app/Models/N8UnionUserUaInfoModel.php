<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class N8UnionUserUaInfoModel extends BaseModel
{
    /**
     * @var bool
     * 是否自增
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
    protected $table = 'n8_union_user_ua_info';
    /**
     * 主键
     *
     * @var string
     */
    protected $primaryKey = 'uuid';
    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'ua_device_id',
        'sys_version',
    ];




}
