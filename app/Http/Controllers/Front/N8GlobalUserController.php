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
}
