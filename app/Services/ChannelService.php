<?php

namespace App\Services;

use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Datas\ChannelData;
use App\Datas\ChannelExtendData;

class ChannelService extends BaseService
{


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
