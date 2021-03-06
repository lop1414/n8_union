<?php

namespace App\Services\Yw;

use App\Common\Services\BaseService;
use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Services\SystemApi\UnionApiService;
use App\Models\ProductModel;

class YwService extends BaseService
{

    /**
     * @var string
     * 平台类型
     */
    protected $cpType = CpTypeEnums::YW;


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
