<?php

namespace App\Services\Qy;

use App\Common\Enums\StatusEnum;
use App\Common\Services\BaseService;
use App\Common\Enums\CpTypeEnums;
use App\Models\ProductModel;

class QyService extends BaseService
{

    /**
     * @var string
     * 平台类型
     */
    protected $cpType = CpTypeEnums::QY;

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
            ->where('status',StatusEnum::ENABLE)
            ->get();
    }
}
