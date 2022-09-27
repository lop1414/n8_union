<?php

namespace App\Services\Cp;


use App\Services\Cp\Chapter\CpChapterInterface;
use App\Services\Cp\Chapter\YwChapterService;
use App\Services\ProductService;


class CpChapterService
{

    private $param;

    private $service;


    public function __construct(CpChapterInterface $service)
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
            YwChapterService::class,
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
    public function getByApi(): array
    {
        $productIds =  $this->getParam('product_ids');
        $where = $productIds
            ? ['product_ids' => $this->getParam('product_ids')]
            : ['cp_type'   => $this->service->getCpType()];

        $productList = ProductService::get($where);

        $bookId = $this->getParam('book_id');

        $data = [];
        foreach ($productList as $product){
            $items = $this->service->get($product,$bookId);
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
