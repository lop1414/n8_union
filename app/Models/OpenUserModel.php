<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class OpenUserModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'open_users';

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
