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
     * 通过cpChannelId,productId 读取渠道信息
     */
    static public function readChannelByCpChannelId($productId,$cpChannelId){

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


    /**
     * @param $productId
     * @param $cpChannelId
     * @return array
     * @throws CustomException
     * 通过cpChannelId,productId 读取渠道信息并检查
     */
    static public function readChannelByCpChannelIdAndCheck($productId,$cpChannelId){
        $channel = self::readChannelByCpChannelId($productId,$cpChannelId);
        if(empty($channel)){
            throw new CustomException([
                'code'       => 'NO_CHANNEL',
                'message'    => "找不到渠道（产品ID:{$productId},N8CP渠道ID:{$cpChannelId}）",
                '#admin_id#' => 0
            ]);
        }

        if(empty($channel['channel_extend'])){

            throw new CustomException([
                'code'       => 'NO_CHANNEL_EXTEND',
                'message'    => "渠道待认领（产品ID:{$productId},N8CP渠道ID:{$cpChannelId}）",
                '#admin_id#' => 0
            ]);
        }
        return $channel;
    }
}
