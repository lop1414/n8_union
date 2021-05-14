<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Common\Enums\StatusEnum;
use App\Datas\ChannelData;
use App\Datas\ChannelExtendData;
use App\Models\ChannelExtendModel;
use Illuminate\Http\Request;

class ChannelExtendController extends FrontController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }



    public function create(Request $request){
        $adminId = $request->get('admin_id');
        $cpChannelId = $request->get('cp_channel_id');
        $advAlias = $request->get('adv_alias');
        $productId = $request->get('product_id');

        $channelModelData = new ChannelData();
        $channelInfo = $channelModelData
            ->setParams([
                'product_id'    => $productId,
                'cp_channel_id' => $cpChannelId
            ])
            ->read();
        if(empty($channelInfo)){
            return $this->fail('FAIL',"渠道不存在 cp_channel_id:{$cpChannelId}");
        }

        $modelData = new ChannelExtendData();
        $channelExtendData = $modelData->setParams(['channel_id' => $channelInfo['id']])->read();
        if(empty($channelExtendData)){
            (new ChannelExtendModel())->create([
                'channel_id'    => $channelInfo['id'],
                'adv_alias'     => $advAlias,
                'status'        => StatusEnum::ENABLE,
                'admin_id'      => $adminId

            ]);
        }

        return $this->success();
    }
}
