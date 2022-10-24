<?php

namespace App\Enums;

class ReadSignTypeEnum
{
    const SIGN_1 = 1;
    const SIGN_2 = 2;
    const SIGN_3 = 3;

    /**
     * @var string
     * 名称
     */
    static public $name = '阅读标记类型';

    /**
     * @var array
     * 列表
     */
    static public $list = [
        ['id' => self::SIGN_1, 'name' => '标记章节1'],
        ['id' => self::SIGN_2, 'name' => '标记章节2'],
        ['id' => self::SIGN_3, 'name' => '标记章节3'],
    ];
}
