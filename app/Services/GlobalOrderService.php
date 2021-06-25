<?php

namespace App\Services;

use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Datas\N8GlobalOrderData;

class GlobalOrderService extends BaseService
{
    protected $modelData;

    public function __construct(){
        parent::__construct();
        $this->modelData = new N8GlobalOrderData();
    }


    /**
     * @param $productId
     * @param $orderId
     * @return mixed|null
     * 制作全局订单信息
     */
    public function make($productId,$orderId){

        $info = $this->read($productId, $orderId);

        if(empty($info)){
            $info = $this->modelData->create($productId,$orderId);
        }

        return $info;
    }



    public function read($productId,$orderId){
        return $this->modelData
            ->setParams(['product_id' => $productId,'order_id' => $orderId])
            ->read();
    }




    /**
     * @param $productId
     * @param $orderId
     * @throws CustomException
     * 清除缓存
     */
    public function clearCache($productId,$orderId){
        (new N8GlobalOrderData())->setParams(['product_id' => $productId,'order_id' => $orderId])->clear();
    }



}
