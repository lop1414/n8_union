<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class BookModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'books';





    protected $fillable = [
        'cp_type',
        'cp_book_id',
        'name',
        'author_name',
        'all_words',
        'update_time'
    ];



    /**
     * 章节 一对多
     */
    public function chapters(){
        return $this->hasMany('App\Models\ChapterModel', 'book_id', 'id');
    }


}
