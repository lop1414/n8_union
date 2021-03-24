<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class UserReadActionModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user_read_actions';


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
        'action_time',
        'cp_book_id',
        'cp_chapter_id',
        'created_at'
    ];




    /**
     * 关联用户信息 一对一
     */
    public function user(){
        return $this->hasOne('App\Models\UserModel', 'n8_guid', 'n8_guid');
    }


    /**
     * 关联用户扩展信息 一对一
     */
    public function user_extend(){
        return $this->hasOne('App\Models\UserExtendModel', 'n8_guid', 'n8_guid');
    }

}
