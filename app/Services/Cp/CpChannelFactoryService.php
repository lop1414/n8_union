<?php

namespace App\Services\Cp;

use App\Common\Enums\CpTypeEnums;
use App\Services\Cp\Channel\BmChannelServiceAbstract;
use App\Services\Cp\Channel\FqChannelServiceAbstract;
use App\Services\Cp\Channel\QyChannelServiceAbstract;
use App\Services\Cp\Channel\TwChannelServiceAbstract;
use App\Services\Cp\Channel\YwChannelServiceAbstract;

class CpChannelFactoryService
{
    static protected $cpChannelServices = [
        CpTypeEnums::BM => [
            'name' => '笔墨',
            'class' => BmChannelServiceAbstract::class
        ],
        CpTypeEnums::TW =>[
            'name' => '腾文',
            'class' => TwChannelServiceAbstract::class
        ],
        CpTypeEnums::QY =>[
            'name' => '七悦',
            'class' => QyChannelServiceAbstract::class
        ],
        CpTypeEnums::YW =>[
            'name' => '阅文',
            'class' => YwChannelServiceAbstract::class
        ],
        CpTypeEnums::FQ =>[
            'name' => '番茄',
            'class' => FqChannelServiceAbstract::class
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
