<?php

namespace App\Enums;

class UserSourceEnum
{
    const WEIXIN_MINI_PROGRAM = 'WEIXIN_MINI_PROGRAM';

    /**
     * @var string
     * 名称
     */
    static public $name = '用户来源';

    /**
     * @var array
     * 列表
     */
    static public $list = [
        ['id' => self::WEIXIN_MINI_PROGRAM, 'name' => '微信小程序'],
    ];
}
