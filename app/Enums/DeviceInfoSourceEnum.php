<?php

namespace App\Enums;

class DeviceInfoSourceEnum
{
    const NETWORK_LICENSE = 'NETWORK_LICENSE';
    const CUSTOM = 'CUSTOM';

    /**
     * @var string
     * 名称
     */
    static public $name = '设备信息';

    /**
     * @var array
     * 列表
     */
    static public $list = [
        ['id' => self::NETWORK_LICENSE, 'name' => '进网许可申请'],
        ['id' => self::CUSTOM, 'name' => '自定义'],
    ];
}
