<?php

namespace App\Services\Yw;

use App\Common\Services\BaseService;
use App\Common\Enums\CpTypeEnums;
use App\Models\ProductModel;

class YwService extends BaseService
{

    /**
     * @var string
     * 平台类型
     */
    protected $cpType = CpTypeEnums::YW;

    protected $product;


    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

    }


    public function setProduct($info){
        $this->product = $info;
        return $this;
    }



    /**
     * 获取产品列表
     *
     * @param array $data
     * @return mixed
     */
    public function getProductList($data = []){

        return (new ProductModel())
            ->where('cp_type',$this->cpType)
            ->where($data)
            ->get();
    }
}
