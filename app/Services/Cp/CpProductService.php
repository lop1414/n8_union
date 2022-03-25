<?php

namespace App\Services\Cp;


use App\Datas\ProductData;
use App\Models\CpAccountModel;
use App\Services\Cp\Product\CpProductInterface;
use App\Services\Cp\Product\YwH5ProductService;
use App\Services\Cp\Product\YwKyyProductService;

class CpProductService
{

    private $param;

    private $service;

    private $modelData;


    public function __construct(CpProductInterface $service)
    {
        $this->service = $service;
        $this->modelData = new ProductData();
    }


    /**
     * @return string[]
     * 获取书城渠道服务列表
     */
    static public function getServices(): array
    {
        return [
            YwKyyProductService::class,
            YwH5ProductService::class,
        ];
    }

    public function __call($name, $arguments)
    {
        return $this->service->$name(...$arguments);
    }

    public function sync()
    {
        $cpType = $this->getParam('cp_type');

        $cpAccounts = (new CpAccountModel())
            ->when($cpType,function ($query,$cpType){
                return $query->where('cp_type',$cpType);
            })
            ->get();

        foreach ($cpAccounts as $cpAccount){
            $data = $this->service->get($cpAccount);

            if(!empty($data)){
                foreach ($data as $item){
                    $this->modelData->save($item);
                }
            }
        }
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
