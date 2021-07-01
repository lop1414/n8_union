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
        return $this->hasMany('App\Models\BookModel', 'id', 'book_id');
    }


}
