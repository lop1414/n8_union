<?php

namespace App\Models;


class UserReadActionModel extends UserActionBaseModel
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
        'uuid',
        'action_time',
        'cp_book_id',
        'cp_chapter_id',
        'extends',
        'created_at'
    ];


    public function setTableNameWithMonth($dateTime){

        $name =  'user_read_actions_'. date('Ym',strtotime($dateTime));
        $this->table = $name;
        return $this;
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

}
