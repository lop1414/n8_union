<?php

namespace App\Services\Check;




use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\StatusEnum;
use App\Datas\ProductData;
use App\Models\ChannelModel;
use Illuminate\Support\Facades\DB;

class ChannelClaimService extends CheckService
{


    public $sendTitle = '请及时认领渠道';


    public function index(){

        $halfHourAgo = date('Y-m-d H:i:s',time() - 60 *30);
        $unclaimedChannelProduct =  (new ChannelModel())
            ->leftJoin('channel_extends AS e','channels.id','=','e.channel_id')
            ->select(DB::raw('channels.*'))
            ->whereNull('e.admin_id')
            ->where('channels.create_time','<=',$halfHourAgo)
            ->get();

        $this->setMarketAllAdmin();

        $content = [];
        foreach ($unclaimedChannelProduct as $channel){

            if($this->isNeedSend($this->getKey($channel['id']))){
                $content[$channel['product_id']][] = $channel->toArray();
            }
        }

        foreach ($content as $productId => $item){
            $product = (new ProductData())->setParams(['id' => $productId])->read();

            // 只检测阅文平台
            if($product['cp_type'] != CpTypeEnums::YW || $product['status'] != StatusEnum::ENABLE) continue;

            $tmpItem = array_chunk($item,'5');

            foreach ($tmpItem as $sendItem){
                $tmp = "产品：{$product['name']}<br>";
                $tmp .= "渠道：<br>";
                foreach ($sendItem as $c){
                    $tmp .= "          {$c['name']}<br>";
                    $this->recordSendLog($this->getKey($c['id']));
                }
                $this->sendContent = $tmp;
                $this->sendMessage();
            }

        }

    }


    public function getKey($id){
        return 'channel_claim:channel_id:'.$id;
    }






}
