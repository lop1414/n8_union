<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class TestBookModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'test_books';





    protected $fillable = [
        'product_id',
        'book_id',
        'description',
        'start_at',
        'end_at',
        'status'
    ];



    /**
     * 章节 一对多
     */
    public function book(){
        return $this->hasOne('App\Models\BookModel', 'id', 'book_id');
    }


    public function test_book_groups(){
        return $this->belongsToMany('App\Models\TestBookGroupModel', 'test_book_test_book_groups', 'test_book_id', 'test_group_id');
    }

}
