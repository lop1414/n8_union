<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class ChapterModel extends BaseModel
{

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'chapters';



    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'book_id',
        'cp_chapter_id',
        'name',
        'seq',
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
     * 书籍 一对一
     */
    public function book(){
        return $this->hasOne('App\Models\BookModel', 'id', 'book_id');
    }



}
