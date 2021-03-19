<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Services\N8GlobalOrderService;
use App\Services\TableCache\N8GlobalOrderTableCacheService;
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
     * 生成 goid
     *
     * @param Request $request
     * @return mixed
     */
    public function make(Request $request){
        $productId = $request->get('product_id');
        $orderId = $request->get('order_id');

        $service = new N8GlobalOrderService();
        $info = $service->make($productId,$orderId);

        return $this->success($info);
    }



    /**
     * 获取信息
     * @param Request $request
     * @return mixed
     */
    public function read(Request $request){
        $by = $request->get('by');
        $goid = $request->get('n8_goid');
        $productId = $request->get('product_id');
        $orderId = $request->get('order_id');

        $service = new N8GlobalOrderTableCacheService();

        if(!empty($by) && $by == 'order_id'){
            $info = $service->getInfoByOrderId($productId,$orderId);
        }else{
            $info = $service->getInfo($goid);
        }

        return $this->success($info);
    }


}
