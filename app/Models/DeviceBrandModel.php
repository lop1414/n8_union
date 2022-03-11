<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class DeviceBrandModel extends BaseModel
{


    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'device_brands';




    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'model',
        'brand',
        'version',
        'source'
    ];
}
