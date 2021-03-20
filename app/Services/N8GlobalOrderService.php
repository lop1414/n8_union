<?php

namespace App\Services;

use App\Common\Services\BaseService;
use App\Models\N8GlobalOrderModel;
use App\Services\TableCache\N8GlobalOrderTableCacheService;

class N8GlobalOrderService extends BaseService
{

    protected $tableCacheService;


    public function __construct(){
        parent::__construct();
        $model = new N8GlobalOrderModel();

        $this->setModel($model);
        $this->tableCacheService = new N8GlobalOrderTableCacheService();

    }




    public function readByOrderId($productId,$orderId){
        $info = $this->tableCacheService->readByOrderId($productId,$orderId);

        if(empty($info)){
            $tmpInfo = $this->model->create([
                'order_id' => $orderId,
                'product_id' => $productId,
            ]);

            $info = $tmpInfo->toArray();
            // 设置缓存
            $this->tableCacheService->setAllTypeCache($info);
        }

        return $info;
    }




    public function read($goid){
        $info = $this->tableCacheService->read($goid);

        return $info;
    }



}
