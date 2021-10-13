<?php

namespace App\Enums;

class N8UserTypeEnum
{
    const NEW = 'NEW';
    const BACKFLOW = 'BACKFLOW';

    /**
     * @var string
     * 名称
     */
    static public $name = 'union 用户类型';

    /**
     * @var array
     * 列表
     */
    static public $list = [
        ['id' => self::NEW, 'name' => '新用户'],
        ['id' => self::BACKFLOW, 'name' => '回流用户'],
    ];
}
