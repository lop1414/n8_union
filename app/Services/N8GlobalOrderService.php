<?php

namespace App\Services;

use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
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




    /**
     * @param $productId
     * @param $orderId
     * @return mixed
     * @throws CustomException
     */
    public function make($productId,$orderId){
        $info = $this->tableCacheService->getInfoByOrderId($productId,$orderId);
        if(!empty($info)){
            throw new CustomException([
                'code'    => 'GOID_EXIST',
                'message' => '订单已存在',
                'log'     => true,
                'data'    => $info
            ]);
        }

        $tmpInfo = $this->model->create([
            'product_id' => $productId,
            'order_id'   => $orderId
        ]);

        $info = $tmpInfo->toArray();

        // 设置缓存
        $this->tableCacheService->setAllTypeCache($info);
        return $info;
    }

}
