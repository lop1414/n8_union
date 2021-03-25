<?php

namespace App\Services;

use App\Common\Helpers\Functions;
use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Datas\ChannelData;
use App\Models\UserLoginActionModel;
use App\Models\UserReadActionModel;
use App\Models\UserShortcutActionModel;

class ChannelService extends BaseService
{

    public function __construct(){
        parent::__construct();
    }



    /**
     * @param $productId
     * @param $cpChannelId
     * @return mixed
     * @throws CustomException
     * 获取渠道ID
     */
    public function readByCpChannelId($productId,$cpChannelId){

        $channel = (new ChannelData())
            ->setParams([
                'product_id'    => $productId,
                'cp_channel_id' => $cpChannelId
            ])
            ->read();

        if(empty($channel)){

            throw new CustomException([
                'code'       => 'NO_CHANNEL',
                'message'    => "找不到渠道（产品ID:{$productId},N8CP渠道ID:{$cpChannelId}）",
                '#admin_id#' => 0
            ]);
        }
        return $channelId = $channel['id'];
    }



    /**
     * 渠道有效变更
     *
     * @param $user
     * @param $channelId
     * @param $dateTime
     * @return mixed
     */
    public function isValidChange($user,$channelId,$dateTime){

        // 变更渠道
        if($user['channel_id'] != $channelId){

            // 保护期内活跃用户
            if($this->isActiveUser($user['n8_guid'],$dateTime)){
                return false;
            }
        }

        return true;
    }



    /**
     * 是否为保护期内活跃用户
     *
     * @param $n8Guid
     * @param $actionTime
     * @return bool
     */
    public function isActiveUser($n8Guid,$actionTime){

        $day = Functions::getProtectPeriod();
        $tmpTimestamp = strtotime($actionTime) - 60*60*24*$day;

        $timeRange = [
            'startTime' => date('Y-m-d H:i:s',$tmpTimestamp),
            'endTime'   => $actionTime
        ];

        //阅读活跃
        $readInfo = (new UserReadActionModel())
            ->where('n8_guid',$n8Guid)
            ->whereBetween('action_time',$timeRange)
            ->first();

        if(!empty($readInfo)) return true;

        //登陆活跃
        $loginInfo = (new UserLoginActionModel())
            ->where('n8_guid',$n8Guid)
            ->whereBetween('action_time',$timeRange)
            ->first();

        if(!empty($loginInfo)) return true;

        //加桌活跃
        $addShortcutInfo = (new UserShortcutActionModel())
            ->where('n8_guid',$n8Guid)
            ->whereBetween('action_time',$timeRange)
            ->first();

        if(!empty($addShortcutInfo)) return true;


        return false;
    }
}
