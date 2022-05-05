<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class ProductCommissionModel extends BaseModel
{
    /**
     * @var bool
     * 不自增
     */
    public $incrementing = false;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'product_commissions';

    protected $primaryKey = 'product_id';


    /**
     * @param $value
     * @return float|int
     * 属性访问器
     */
    public function getDivideAttribute($value)
    {
        return $value / 100;
    }

    /**
     * @param $value
     * 属性修饰器
     */
    public function setDivideAttribute($value)
    {
        $this->attributes['divide'] = intval($value * 100);
    }
}
