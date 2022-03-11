<?php

namespace App\Models;


class N8UnionUserModel extends UserActionBaseModel
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
        'product_id',
        'channel_id',
        'created_time',
        'book_id',
        'chapter_id',
        'force_chapter_id',
        'platform',
        'admin_id',
        'adv_alias',
        'click_id',
        'matcher',
        'last_match_time',
        'user_type',
        'sys_version',
        'device_model',
        'created_at'
    ];


    /**
     * 关联用户扩展信息 一对一
     */
    public function extend(){
        return $this->hasOne('App\Models\N8UnionUserExtendModel', 'uuid', 'id');
    }



    /**
     * 关联模型   多对一
     */
    public function device_brand(){
        return $this->belongsTo('App\Models\DeviceBrandModel', 'model', 'device_model');
    }


    /**
     * 关联模型   多对一
     */
    public function device_name(){
        return $this->belongsTo('App\Models\DeviceNameModel', 'model', 'device_model');
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
