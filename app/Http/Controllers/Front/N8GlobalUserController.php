<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Services\N8GlobalUserService;
use Illuminate\Http\Request;

class N8GlobalUserController extends FrontController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }



    /**
     * 制作 guid
     *
     * @param Request $request
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     */
    public function make(Request $request){
        $productId = $request->get('product_id');
        $openId = $request->get('open_id');

        $service = new N8GlobalUserService();
        $info = $service->make($productId,$openId);

        return $this->success($info);
    }


    /**
     * 获取信息
     * @param Request $request
     * @return mixed
     */
    public function read(Request $request){
        $by = $request->get('by');
        $guid = $request->get('n8_guid');
        $productId = $request->get('product_id');
        $openId = $request->get('open_id');

        $service = new N8GlobalUserService();

        if(!empty($by) && $by == 'open_id'){
            $info = $service->readByOpenId($productId,$openId);
        }else{
            $info = $service->read($guid);
        }

        return $this->success($info);
    }



    /**
     * 删除信息
     * @param Request $request
     * @return mixed
     */
    public function del(Request $request){
        $by = $request->get('by');
        $guid = $request->get('n8_guid');
        $productId = $request->get('product_id');
        $openId = $request->get('open_id');

        $service = new N8GlobalUserService();

        if(!empty($by) && $by == 'open_id'){
            $info = $service->delByOpenId($productId,$openId);
        }else{
            $info = $service->del($guid);
        }

        return $this->success($info);
    }


}
