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
     * @param $value
     * @return string
     * 属性访问器
     */
    public function getCpBookIdAttribute($value)
    {
        return "{$value}";
    }

    /**
     * 章节 一对多
     */
    public function chapters(){
        return $this->hasMany('App\Models\ChapterModel', 'book_id', 'id');
    }


    public function book_lable()
    {
        return $this->belongsToMany('App\Models\BookLabelModel','book_book_labels','book_id','book_label_id');
    }

}
