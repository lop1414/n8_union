<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class N8UnionUserExtendModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'n8_union_user_extends';


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
        'uuid',
        'ip',
        'ua',
        'muid',
        'oaid',
        'click_id',
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
    ];

}
