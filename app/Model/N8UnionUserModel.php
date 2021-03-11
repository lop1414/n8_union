<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class N8UnionUserModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'n8_union_users';


    /**
     * 禁用默认更新时间
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'n8_guid',
        'channel_id',
        'created_time',
        'book_id',
        'chapter_id',
        'force_chapter_id',
        'admin_id',
        'click_id',
        'created_at'
    ];


    /**
     * 关联用户扩展信息 一对一
     */
    public function extend(){
        return $this->hasOne('App\Models\N8UnionUserExtendModel', 'uuid', 'id');
    }




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
        return $this->hasOne('App\Models\ChapterModel', 'id', 'chapter_id');
    }


    /**
     * 强制章节
     */
    public function force_chapter(){
        return $this->hasOne('App\Models\ChapterModel', 'id', 'force_chapter_id');
    }

}
