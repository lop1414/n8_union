<?php


namespace App\Enums;


class MenuLevelEnums
{
    const COMMON = 'COMMON';
    const DEFAULT = 'DEFAULT';
    const CP_TYPE = 'CP_TYPE';
    const PRODUCT_TYPE = 'PRODUCT_TYPE';
    const PRODUCT = 'PRODUCT';

    /**
     * @var string
     * 名称
     */
    static public $name = '菜单级别类型';

    /**
     * @var array
     * 列表
     */
    static public $list = [
        ['id' => self::COMMON, 'name' => '公共'],
        ['id' => self::DEFAULT, 'name' => '默认'],
        ['id' => self::CP_TYPE, 'name' => '平台'],
        ['id' => self::PRODUCT_TYPE, 'name' => '产品类型'],
        ['id' => self::PRODUCT, 'name' => '产品'],
    ];

}
