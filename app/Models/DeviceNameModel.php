<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class DeviceNameModel extends BaseModel
{


    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'device_names';



    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'model',
        'name',
        'version',
        'source'
    ];
}
