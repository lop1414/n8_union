<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class TestBookGroupModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'test_book_groups';



    protected $fillable = [
        'name',
        'status'
    ];


    public function test_books(){
        return $this->belongsToMany('App\Model\TestBookModel');
    }

}
