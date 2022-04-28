<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class ProductMoneyDivideModel extends BaseModel
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
    protected $table = 'product_money_divide';
    protected $primaryKey = 'product_id';

}
