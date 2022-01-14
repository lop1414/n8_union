<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Common\Enums\ResponseCodeEnum;
use App\Datas\ChannelData;
use App\Datas\ChannelExtendData;
use App\Models\ChannelModel;
use Illuminate\Http\Request;

class ChannelController extends FrontController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function read(Request $request){
        $id = $request->get('id');
        $productId = $request->get('product_id');
        $cpChannelId = $request->get('cp_channel_id');
        $params = [];
        if($id){
            $params['id'] = $id;
        }elseif (!empty($productId) && !empty($cpChannelId)){
            $params['product_id'] = $productId;
            $params['cp_channel_id'] = $cpChannelId;
        }else{
            $this->fail(ResponseCodeEnum::FAIL,'参数错误');
        }
        $channel = (new ChannelData())->setParams($params)->read();
        $extends = [];
        if(!empty($channel)){
            $extends = (new ChannelExtendData())->setParams(['channel_id'=>$channel['id']])->read();
        }
        $channel['channel_extends'] = $extends;
        return $this->success($channel);
    }


    public function get(Request $request){
        $id = $request->get('id');
        $productId = $request->get('product_id');
        $cpChannelId = $request->get('cp_channel_id');
        $channel = (new ChannelModel())
            ->makeVisible('cp_secret')
            ->when($id,function ($query,$id){
                return $query->where('id',$id);
            })
            ->when($productId,function ($query,$productId){
                return $query->where('product_id',$productId);
            })
            ->when($cpChannelId,function ($query,$cpChannelId){
                return $query->where('cp_channel_id',$cpChannelId);
            })
            ->get();

        return $this->success($channel);
    }



}
