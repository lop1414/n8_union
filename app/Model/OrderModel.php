<?php

namespace App\Models;


class OrderModel extends UserActionBaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'orders';

    /**
     * 关联到模型数据表的主键
     *
     * @var string
     */
    protected $primaryKey = 'n8_goid';


    /**
     * @var bool
     * 是否自增
     */
    public $incrementing = false;


    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'n8_guid',
        'n8_goid',
        'product_id',
        'channel_id',
        'order_id',
        'order_time',
        'amount',
        'type',
        'status',
        'complete_time',
        'adv_alias',
        'click_id',
        'complete_click_id',
    ];


    /**
     * 隐藏字段
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];


    /**
     * @param $value
     * @return float|int
     * 属性访问器
     */
    public function getAmountAttribute($value)
    {
        return round($value/100,2);
    }


    public function global_order(){
        return $this->hasOne('App\Models\N8GlobalOrderModel', 'n8_goid', 'n8_goid');
    }


    /**
     * 关联订单扩展 一对一
     */
    public function extend(){
        return $this->hasOne('App\Models\OrderExtendModel', 'n8_goid', 'n8_goid');
    }
}
