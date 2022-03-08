<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class DeviceModel extends BaseModel
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
    protected $table = 'devices';
    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'brand',
        'model',
    ];
}
