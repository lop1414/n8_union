<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class DeviceCustomNameModel extends BaseModel
{


    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'device_custom_names';



    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'model';




    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'model',
        'name'
    ];
}
