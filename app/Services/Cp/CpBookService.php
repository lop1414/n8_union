<?php

namespace App\Services\Cp;


use App\Services\Cp\Book\CpBookInterface;
use App\Services\Cp\Book\FqBookService;
use App\Services\Cp\Book\YwBookService;
use App\Services\ProductService;


class CpBookService
{

    private $param;

    private $service;


    public function __construct(CpBookInterface $service)
    {
        $this->service = $service;
    }


    /**
     * @return string[]
     * 获取书城渠道服务列表
     */
    static public function getServices(): array
    {
        return [
            FqBookService::class,
            YwBookService::class,
        ];
    }

    public function __call($name, $arguments)
    {
        return $this->service->$name(...$arguments);
    }

    /**
     * 获取接口数据
     * @return array
     */
    public function readByApi(): array
    {
        $productIds =  $this->getParam('product_ids');
        $where = $productIds
            ? ['product_ids' => $this->getParam('product_ids')]
            : ['cp_type'   => $this->service->getCpType()];

        $productList = ProductService::get($where);

        $cpId = $this->getParam('cp_id');

        $data = [];
        foreach ($productList as $product){
            $items = $this->service->read($product,$cpId);
            $data = array_merge($data,$items);
        }
        return $data;
    }

    public function getParam($key)
    {
        if(empty($this->param[$key])){
            return null;
        }
        return $this->param[$key];
    }

    public function setParam($key,$data)
    {
        $this->param[$key] = $data;
    }
}
