<?php

namespace App\Services\Cp\Product;

use App\Common\Enums\MatcherEnum;
use App\Common\Enums\OperatorEnum;
use App\Common\Enums\StatusEnum;
use App\Datas\ProductData;
use App\Models\ProductModel;
use App\Services\Cp\CpBaseService;

class CpProductBaseService extends CpBaseService
{

    public function save($data){

        $model = new ProductModel();
        $pro = $model
            ->where('cp_account_id',$data['cp_account_id'])
            ->where('cp_product_alias',$data['cp_product_alias'])
            ->where('type',$data['type'])
            ->first();

        if(empty($pro)){
            $pro = $model;
            $pro->cp_account_id = $data['cp_account_id'];
            $pro->cp_product_alias = $data['cp_product_alias'];
            $pro->cp_type = $data['cp_type'];
            $pro->type = $data['type'];
            $pro->secret = md5(uniqid());
            $pro->status = StatusEnum::DISABLE;
            $pro->matcher = MatcherEnum::SYS;
            $pro->operator = OperatorEnum::SYS;
        }
        $pro->name = $data['name'];
        $pro->save();
    }



    public function syncPrepare(){
        $this->syncAfter(function (){
            // 清除所有产品缓存
            (new ProductData())->clearAll();
        });
    }


}
