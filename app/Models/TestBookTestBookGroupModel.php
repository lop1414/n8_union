<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class TestBookTestBookGroupModel extends BaseModel
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'test_book_test_book_groups';


    /**
     * 禁用默认更新时间
     *
     * @var bool
     */
    public $timestamps = false;



    protected $fillable = [
        'test_book_id',
        'test_group_id'
    ];

}
