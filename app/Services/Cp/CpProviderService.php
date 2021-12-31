<?php

namespace App\Services\Cp;

use App\Common\Enums\CpTypeEnums;
use App\Services\Cp\Channel\BmChannelService;
use App\Services\Cp\Channel\FqChannelService;
use App\Services\Cp\Channel\QyChannelService;
use App\Services\Cp\Channel\TwChannelService;
use App\Services\Cp\Channel\YwChannelService;
use App\Services\Cp\Book\FqBookService;
use App\Services\Cp\Book\QyBookService;
use App\Services\Cp\Book\YwBookService;
use App\Services\Cp\Chapter\FqChapterService;
use App\Services\Cp\Chapter\QyChapterService;
use App\Services\Cp\Chapter\YwChapterService;
use App\Services\Cp\Product\YwProductService;

class CpProviderService
{
    static protected $cpServices = [
        CpTypeEnums::BM => [
            'name' => '笔墨',
            'class' => [
                'channel'   => BmChannelService::class
            ]
        ],
        CpTypeEnums::TW =>[
            'name' => '腾文',
            'class' => [
                'channel'   => TwChannelService::class
            ]
        ],
        CpTypeEnums::QY =>[
            'name' => '七悦',
            'class' => [
                'channel'   => QyChannelService::class,
                'book'      => QyBookService::class,
                'chapter'   => QyChapterService::class
            ]
        ],
        CpTypeEnums::YW =>[
            'name' => '阅文',
            'class' => [
                'product'   => YwProductService::class,
                'channel'   => YwChannelService::class,
                'book'      => YwBookService::class,
                'chapter'   => YwChapterService::class,
            ]
        ],
        CpTypeEnums::FQ =>[
            'name' => '番茄',
            'class' => [
                'channel'   => FqChannelService::class,
                'book'      => FqBookService::class,
                'chapter'   => FqChapterService::class,
            ]
        ]
    ];


    static protected $cpServicesExample = [];

    static public function readCpProductService($cpType){
        return self::readCpService('product',$cpType);
    }

    /**
     * @param null $cpType
     * @return array|string[]
     */
    static public function readCpService($type,$cpType){
        $key = $cpType.'-'.$type;
        if(!isset(self::$cpServicesExample[$key])){
            $service = self::$cpServices[$cpType]['class'][$type] ?? null;
            self::$cpServicesExample[$key] = is_null($service) ? null : (new $service);
        }
        return self::$cpServicesExample[$key];
    }

    static public function readCpChannelService($cpType){
        return self::readCpService('channel',$cpType);
    }



    static public function readCpBookService($cpType){
        return self::readCpService('book',$cpType);
    }



    static public function readCpChapterService($cpType){
        return self::readCpService('chapter',$cpType);
    }

    static public function getCpChannelServices(){
        return self::getCpServices('channel');
    }

    /**
     * @return string[][]
     */
    static public function getCpServices($type){
        $arr = [];
        foreach (self::$cpServices as $cpType => $item){
            if(!isset($item['class'][$type])) continue;
            $arr[] = new $item['class'][$type];
        }
        return $arr;
    }

}
