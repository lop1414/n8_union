<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class CpChannelModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'cp_channels';


    /**
     * 渠道
     */
    public function channel(){
        return $this->hasOne('App\Models\ChannelModel', 'gcid', 'id');
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
