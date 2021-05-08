<?php

namespace App\Services\Tw;

use App\Common\Services\BaseService;
use App\Common\Enums\CpTypeEnums;
use App\Models\ProductModel;

class TwService extends BaseService
{

    /**
     * @var string
     * 平台类型
     */
    protected $cpType = CpTypeEnums::TW;


    /**
     * constructor.
     */
    public function __construct(){
        parent::__construct();

    }



    /**
     * 获取产品列表
     *
     * @param array $data
     * @return mixed
     */
    public function getProductList($data = []){

        return  (new ProductModel())
            ->where('cp_type',$this->cpType)
            ->where($data)
            ->get();

    }
}
