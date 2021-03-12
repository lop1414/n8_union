<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Common\Traits\ValidRule;
use App\Models\ChannelModel;
use App\Models\CpChannelModel;
use Illuminate\Http\Request;

class N8ChannelController extends FrontController
{
    use ValidRule;

    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }



    public function read(Request $request){
        $req = $request->all();
        $this->validRule($req,[
            'id'    =>  'required'
        ]);

        $model = new ChannelModel();
        $channel = $model
            ->where('id',$req['id'])
            ->first();

        return $this->success($channel);
    }




    public function readByCpChannel(Request $request){
        $req = $request->all();
        $this->validRule($req,[
            'cp_channel_id'    =>  'required',
            'product_id'       =>  'required'
        ]);

        $channel = [];

        $model = new CpChannelModel();
        $cpChannel = $model
            ->where('product_id',$req['product_id'])
            ->where('cp_channel_id',$req['cp_channel_id'])
            ->first();

        if($cpChannel){
            $channel = $cpChannel->channel;
        }

        return $this->success($channel);
    }


}
