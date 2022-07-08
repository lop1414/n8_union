<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\MorphPivot;

class BookBookLabelModel extends MorphPivot
{
    /**
     * 禁用默认更新时间
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'book_book_labels';

    /**
     * @return string
     * 获取主键
     */
    public function getPrimaryKey(){
        return $this->primaryKey;
    }

}
