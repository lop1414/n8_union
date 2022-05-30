<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class BookCommissionModel extends BaseModel
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
    protected $table = 'book_commissions';

    protected $primaryKey = 'book_id';


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


    /**
     * 书籍
     */
    public function book(){
        return $this->hasOne('App\Models\BookModel', 'id', 'book_id');
    }
}
