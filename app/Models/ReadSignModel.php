<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class ReadSignModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'read_signs';

    protected $fillable = [
        'name',
        'book_id',
        'sign_chapter_id_1',
        'sign_chapter_id_2',
        'sign_chapter_id_3'
    ];


    /**
     * 书籍
     */
    public function book(){
        return $this->hasOne('App\Models\BookModel', 'id', 'book_id');
    }


    /**
     * 标记1
     */
    public function sign_chapter_1(){
        return $this->hasOne('App\Models\ChapterModel', 'id', 'sign_chapter_id_1');
    }

    /**
     * 标记2
     */
    public function sign_chapter_2(){
        return $this->hasOne('App\Models\ChapterModel', 'id', 'sign_chapter_id_2');
    }

    /**
     * 标记3
     */
    public function sign_chapter_3(){
        return $this->hasOne('App\Models\ChapterModel', 'id', 'sign_chapter_id_3');
    }

}
