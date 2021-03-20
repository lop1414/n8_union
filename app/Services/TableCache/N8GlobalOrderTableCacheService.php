<?php

namespace App\Services\TableCache;

use App\Common\Services\BaseService;
use App\Common\Tools\CustomException;
use App\Common\Tools\TableCache;
use App\Models\N8GlobalOrderModel;

class N8GlobalOrderTableCacheService extends BaseService
{

    protected $tableCacheTool,$tableCacheToolByOrderId;

    public function __construct(){
        parent::__construct();
        $model = new N8GlobalOrderModel();
        $this->setModel($model);
        $this->tableCacheTool = new TableCache($model,'n8_goid');
        $this->tableCacheToolByOrderId = new TableCache($model,'order_id');
    }


    public function read($goid){
        return $this->tableCacheTool->get($goid);

    }



    public function set($data){
        return $this->tableCacheTool->set($data['n8_goid'],$data);
    }




    public function getKeyByOrderId($data){
        return  $data['product_id'].'-'.$data['order_id'];
    }


    public function readByOrderId($productId,$orderId){
        $key = $this->getKeyByOrderId([
            'product_id' => $productId,
            'order_id'   => $orderId
        ]);
        return $this->tableCacheToolByOrderId->get($key);
    }


    public function setByOrderId($data){
        $key = $this->getKeyByOrderId($data);
        return $this->tableCacheToolByOrderId->set($key,$data);
    }




    /**
     * 设置所有类型缓存
     *
     * @param $data
     */
    public function setAllTypeCache($data){
        $this->set($data);
        $this->setByOrderId($data);
    }




    /**
     * 是否存在
     * @param $productId
     * @param $orderId
     * @return bool
     * @throws CustomException
     */
    public function isExistByOrderId($productId,$orderId){
        $guidInfo = $this->readByOrderId($productId,$orderId);

        if(empty($guidInfo)){
            throw new CustomException([
                'code'    => 'NOT_GUID_BY_ORDER_ID',
                'message' => '找不到订单',
                'log'     => true,
                'data'    => [
                    'product_id' => $productId,
                    'order_id'    => $orderId
                ]
            ]);
        }
        return $guidInfo;
    }




    public function refreshAllCache(){

        // 删除
        $tmpList = $this->tableCacheTool->keys();

        foreach ($tmpList as $cacheKey){
            $this->tableCacheTool->del($cacheKey);
        }

        unset($tmpList);

        $tmpList = $this->tableCacheToolByOrderId->keys();

        foreach ($tmpList as $cacheKey){
            $this->tableCacheTool->del($cacheKey);
        }
        unset($tmpList);

        // 创建
        $page = 1;
        do{
            $data = $this->getModel()->listPage($page,10000);
            $items = $data['list']->toArray();
            foreach ($items as $item){
                $this->setAllTypeCache($item);
            }

            $page +=1;
        }while($data['page_info']['total_page'] >= $page);

    }

}
