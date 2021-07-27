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


    public function create(Request $request){
        $req = $request->all();

        $this->validRule($req,[
            'product_id'    =>  'required',
            'cp_channel_id'      =>  'required',
        ]);

        $model = (new ChannelModel())
            ->where('product_id',$req['product_id'])
            ->where('cp_channel_id',$req['cp_channel_id'])
            ->first();


        if(empty($model)){
            $model = new ChannelModel();
        }

        $model->product_id = $req['product_id'];
        $model->cp_channel_id = $req['cp_channel_id'];
        $model->name = $req['name'];
        $model->book_id = $req['book_id'];
        $model->chapter_id = $req['chapter_id'];
        $model->force_chapter_id = $req['force_chapter_id'];
        $model->create_time = $req['create_time'];
        $model->updated_time = $req['updated_time'];
        $model->save();

        return $this->success($model);
    }
}
