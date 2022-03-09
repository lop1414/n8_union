<?php

namespace App\Models;

use App\Common\Models\BaseModel;

class DeviceNetworkLicenseModel extends BaseModel
{
    /**
     * 禁用默认更新时间
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'device_network_license';



    /**
     * 可以被批量赋值的属性。
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'model',
        'apply_org',
        'reg_date',
        'end_date',
        'license_no',
    ];
}
