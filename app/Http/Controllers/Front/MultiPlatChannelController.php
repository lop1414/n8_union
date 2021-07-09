<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Common\Enums\ResponseCodeEnum;
use App\Datas\MultiPlatFormChannelData;
use Illuminate\Http\Request;

class MultiPlatChannelController extends FrontController
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
        $params = [];
        if($id){
            $params['id'] = $id;
        }else{
            $this->fail(ResponseCodeEnum::FAIL,'参数错误');
        }

        $channel = (new MultiPlatFormChannelData())->setParams($params)->read();
        return $this->success($channel);
    }



}
