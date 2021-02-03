<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class N8GlobalOrderModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'n8_global_orders';

    /**
     * 关联到模型数据表的主键
     *
     * @var string
     */
    protected $primaryKey = 'n8_goid';


    /**
     * 禁用默认更新时间
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'product_id'
    ];



    /**
     * 关联产品模型 一对一
     */
    public function product(){
        return $this->hasOne('App\Models\ProductModel', 'id', 'product_id');
    }
}
