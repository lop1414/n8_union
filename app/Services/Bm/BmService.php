<?php

namespace App\Services\Bm;

use App\Common\Services\BaseService;
use App\Common\Enums\CpTypeEnums;
use App\Models\ProductModel;

class BmService extends BaseService
{

    /**
     * @var string
     * 平台类型
     */
    protected $cpType = CpTypeEnums::BM;


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

        $list = (new ProductModel())
            ->where('cp_type',$this->cpType)
            ->where($data)
            ->get();

        return $list;
    }
}
