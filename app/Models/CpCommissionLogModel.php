<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class CpCommissionLogModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'cp_commission_logs';



    /**
     * @param $value
     * @return float|int
     * 属性访问器
     */
    public function getCommissionAttribute($value)
    {
        return $value / 100;
    }

    /**
     * @param $value
     * 属性修饰器
     */
    public function setCommissionAttribute($value)
    {
        $this->attributes['commission'] = intval($value * 100);
    }

}
