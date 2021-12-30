<?php

namespace App\Services;



use App\Common\Services\BaseService;
use App\Datas\ProductData;


class ProductService extends BaseService
{
    static protected $productMap;

    static protected $productMapByAlias;

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



    static public function readByAlias($alias,$cpType){
        $key = $cpType.'-'.$alias;
        if(empty(self::$productMapByAlias[$key])){
            self::$productMap[$key] = (new ProductData())
                ->setParams(['cp_product_alias'  => $alias, 'cp_type' => $cpType])
                ->read();
        }
        return self::$productMap[$key];
    }
}
