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




    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'cp_type',
        'cp_book_id',
        'name',
        'author_name',
        'all_words',
        'update_time'
    ];


    /**
     * 隐藏字段
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];



    /**
     * 章节 一对多
     */
    public function chapters(){
        return $this->hasMany('App\Models\ChapterModel', 'book_id', 'id');
    }


}
