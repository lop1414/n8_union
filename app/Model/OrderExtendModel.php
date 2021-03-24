<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class OrderExtendModel extends BaseModel
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'order_extends';

    /**
     * 关联到模型数据表的主键
     *
     * @var string
     */
    protected $primaryKey = 'n8_goid';


    /**
     * @var bool
     * 是否自增
     */
    public $incrementing = false;


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
        'n8_goid',
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
    ];

}
