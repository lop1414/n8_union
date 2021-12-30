<?php

namespace App\Services;



use App\Common\Services\BaseService;
use App\Datas\ProductData;


class ProductService extends BaseService
{
    static protected $productMap;

    static public function readToType($id){
        $product = self::read($id);
        return $product['type'];
    }

    static public function read($id){
        if(empty(self::$productMap[$id])){
            self::$productMap[$id] = (new ProductData())->setParams(['id' => $id])->read();
        }
        return self::$productMap[$id];
    }
}
