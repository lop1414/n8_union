<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Services\N8GlobalOrderService;
use Illuminate\Http\Request;

class N8GlobalOrderController extends FrontController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }



    /**
     * 制作 goid
     *
     * @param Request $request
     * @return mixed
     * @throws \App\Common\Tools\CustomException
     */
    public function make(Request $request){
        $productId = $request->get('product_id');
        $orderId = $request->get('order_id');

        $service = new N8GlobalOrderService();
        $info = $service->make($productId,$orderId);

        return $this->success($info);
    }
}
