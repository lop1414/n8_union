<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class UaDeviceLogModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'ua_device_logs';


    /**
     * @param $value
     * @return array
     * 属性访问器
     */
    public function getExtendsAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * @param $value
     * 属性修饰器
     */
    public function setExtendsAttribute($value)
    {
        $this->attributes['extends'] = json_encode($value);
    }

}
