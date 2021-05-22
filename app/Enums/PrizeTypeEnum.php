<?php

namespace App\Enums;

class PrizeTypeEnum
{
    const IN_KIND = 'IN_KIND';
    const BOOK_COIN = 'BOOK_COIN';

    /**
     * @var string
     * 名称
     */
    static public $name = '抽奖奖品类型';

    /**
     * @var array
     * 列表
     */
    static public $list = [
        ['id' => self::IN_KIND, 'name' => '实物'],
        ['id' => self::BOOK_COIN, 'name' => '书币'],
    ];
}
