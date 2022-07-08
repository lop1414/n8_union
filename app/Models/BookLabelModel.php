<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class BookLabelModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'book_labels';


    public function books()
    {
        return $this->belongsToMany('App\Models\BookModel','book_book_labels','book_label_id','book_id')->using('App\Models\BookBookLabelModel');
    }
}
