<?php

namespace App\Services;

use App\Common\Enums\CpTypeEnums;
use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Datas\ChannelData;
use App\Datas\ChannelExtendData;
use App\Services\Cp\Channel\BmChannelService;
use App\Services\Cp\Channel\QyChannelService;
use App\Services\Cp\Channel\TwChannelService;
use App\Services\Cp\Channel\YwChannelService;

class ChannelService extends BaseService
{

    protected $cpChannelServices = [
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
        ]
    ];


    /**
     * @param null $cpType
     * @return array|string[]
     */
    public function getCpService($cpType = null){

        if(empty($this->cpChannelServices[$cpType])) return [''];

        return $this->cpChannelServices[$cpType];
    }

    /**
     * @return string[][]
     */
    public function getAllCpServices(){
        return $this->cpChannelServices;
    }


    /**
     * @param $productId
     * @param $cpChannelId
     * @return array|null
     * @throws CustomException
     */
    public function getChannelByCpChannelId($productId,$cpChannelId){

        if(empty($cpChannelId)){
            return [];
        }

        $channel = (new ChannelData())
            ->setParams(['product_id'=> $productId, 'cp_channel_id'=>$cpChannelId])
            ->read();

        $channelExtend = (new ChannelExtendData())->setParams(['channel_id' => $channel['id']])->read();

        $channel['channel_extend'] = $channelExtend;

        return $channel;
    }
}
