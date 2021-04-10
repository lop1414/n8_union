<?php

namespace App\Models;


class UserLoginActionModel extends UserActionBaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user_login_actions';



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
        'adv_alias',
        'ip',
        'ua',
        'muid',
        'oaid',
        'device_brand',
        'device_manufacturer',
        'device_model',
        'device_product',
        'device_os_version_name',
        'device_os_version_code',
        'device_platform_version_name',
        'device_platform_version_code',
        'android_id',
        'request_id',
        'created_at'
    ];



    public function setTableNameWithMonth($dateTime){

        $name =  'user_login_actions_'. date('Ym',strtotime($dateTime));
        $this->table = $name;
        return $this;
    }


}
