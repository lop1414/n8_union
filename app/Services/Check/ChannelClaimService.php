<?php

namespace App\Services\Check;


use App\Common\Enums\AdvAliasEnum;
use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\StatusEnum;
use App\Common\Services\SystemApi\AdvOceanApiService;
use App\Datas\ChannelData;
use App\Datas\ProductData;
use App\Models\ChannelExtendModel;

class ChannelClaimService extends CheckService
{


    public $sendTitle = '渠道认领提示';


    public function index(){

        $startTime = date('Y-m-d H:i:s',time() - 60 * 60 * 12);
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

                $channel = (new ChannelData())->setParams(['id'=>$item['channel_id']])->read();
                $product = (new ProductData())->setParams(['id' => $channel['product_id']])->read();
                // 跳过不是阅文产品
                if($product['cp_type'] != CpTypeEnums::YW) continue;

                $channelExtendModel = new ChannelExtendModel();
                $channelExtendModel->channel_id = $item['channel_id'];
                $channelExtendModel->adv_alias = AdvAliasEnum::OCEAN;
                $channelExtendModel->status = StatusEnum::ENABLE;
                $channelExtendModel->admin_id = $item['admin_id'];
//                $channelExtendModel->save();




                $tmp = "渠道：{$channel['name']}<br>";
                $tmp .= "<br>投放信息：<br>";
                $tmp .= "产品： {$product['name']}<br>";
                $tmp .= "账户： {$item['account_name']}<br>";
                $tmp .= "计划： {$item['ad_name']}<br>";

                $tmp .= "<br>";
                $tmp .= "默认认领信息：<br>";
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
