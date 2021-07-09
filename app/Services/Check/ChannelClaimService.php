<?php

namespace App\Services\Check;


use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\StatusEnum;
use App\Common\Services\SystemApi\AdvOceanApiService;
use App\Datas\ChannelData;
use App\Models\ChannelExtendModel;

class ChannelClaimService extends CheckService
{


    public $sendTitle = '渠道认领提示';


    public function index(){

        $startTime = date('Y-m-d H:i:s',time() - 60 * 60 * 24);
        $endTime = date('Y-m-d H:i:s');

        $time = $startTime;
        while($time < $endTime){
            $tmpEndTime = date('Y-m-d H:i:s',  strtotime($time) + 60 * 60 *2);


            $list = (new AdvOceanApiService())->apiGetChannelAds($time,$tmpEndTime);

            $advNameMap = array_column(AdvAliasEnum::$list,'name','id');
            $statusNameMap = array_column(StatusEnum::$list,'name','id');

            foreach ($list as  $item){
                $channelExtend = (new ChannelExtendModel())->where('channel_id',$item['channel_id'])->first();
                if(!empty($channelExtend)){
                    continue;
                }

                $channelExtendModel = new ChannelExtendModel();
                $channelExtendModel->channel_id = $item['channel_id'];
                $channelExtendModel->adv_alias = AdvAliasEnum::OCEAN;
                $channelExtendModel->status = StatusEnum::ENABLE;
                $channelExtendModel->admin_id = $item['admin_id'];
//                $channelExtendModel->save();


                $channel = (new ChannelData())->setParams(['id'=>$item['channel_id']])->read();

                $tmp = "检测到渠道 {$channel['name']} 正在投放，已默认认领<br>";
                $tmp .= "广告商：".$advNameMap[$channelExtendModel->adv_alias]."<br>";
                $tmp .= "状态：".$statusNameMap[$channelExtendModel->status]."<br>";
//                $this->sendAdminIds=[25,$channelExtendModel->admin_id];
                $this->sendAdminIds=[25];
                $this->sendContent = $tmp;
                $this->sendMessage();
            }

            $time = $tmpEndTime;
        }
    }


    public function getKey($id){
        return 'channel_claim:channel_id:'.$id;
    }






}
