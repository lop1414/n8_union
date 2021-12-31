<?php

namespace App\Services\Cp;

use App\Common\Enums\CpTypeEnums;
use App\Services\Cp\Channel\BmChannelService;
use App\Services\Cp\Channel\FqChannelService;
use App\Services\Cp\Channel\QyChannelService;
use App\Services\Cp\Channel\TwChannelService;
use App\Services\Cp\Channel\YwChannelService;

class CpChannelFactoryService
{
    static protected $cpChannelServices = [
        CpTypeEnums::BM => [
            'name' => '笔墨',
            'class' => BmChannelService::class
        ],
        CpTypeEnums::TW =>[
            'name' => '腾文',
            'class' => TwChannelService::class
        ],
        CpTypeEnums::QY =>[
            'name' => '七悦',
            'class' => QyChannelService::class
        ],
        CpTypeEnums::YW =>[
            'name' => '阅文',
            'class' => YwChannelService::class
        ],
        CpTypeEnums::FQ =>[
            'name' => '番茄',
            'class' => FqChannelService::class
        ]
    ];



    /**
     * @param null $cpType
     * @return array|string[]
     */
    static public function readCpService($cpType = null){

        if(empty(self::$cpChannelServices[$cpType])) return [''];

        return self::$cpChannelServices[$cpType];
    }



    /**
     * @return string[][]
     */
    static public function getCpServices(){
        return self::$cpChannelServices;
    }
}
