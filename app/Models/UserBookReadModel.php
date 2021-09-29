<?php

namespace App\Models;


use App\Common\Models\BaseModel;

class UserBookReadModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user_book_reads';


    /**
     * 书籍
     */
    public function book(){
        return $this->hasOne('App\Models\BookModel', 'id', 'book_id');
    }


    /**
     * 章节
     */
    public function chapter(){
        return $this->hasOne('App\Models\ChapterModel', 'id', 'last_chapter_id');
    }

}
