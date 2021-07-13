<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class ChannelModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'channels';



    protected $fillable = [
        'product_id',
        'cp_channel_id',
        'name',
        'book_id',
        'chapter_id',
        'force_chapter_id',
        'extends',
        'create_time',
        'updated_time',
    ];


    protected $appends = ['href_url'];



    public function getHrefUrlAttribute()
    {
        $extends = $this->attributes['extends'] ?? [];
        if(empty($extends)) return '';
        $extends = $this->getExtendsAttribute($extends);
        return $extends->hap_url;
    }


    /**
     * @param $value
     * @return array
     * 属性访问器
     */
    public function getExtendsAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * @param $value
     * 属性修饰器
     */
    public function setExtendsAttribute($value)
    {
        $this->attributes['extends'] = json_encode($value);
    }



    public function channel_extend(){
        return $this->hasOne('App\Models\ChannelExtendModel', 'channel_id', 'id');
    }


    /**
     * 产品
     */
    public function product(){
        return $this->hasOne('App\Models\ProductModel', 'id', 'product_id');
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
