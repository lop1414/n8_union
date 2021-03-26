<?php

namespace App\Services;

use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Enums\StatusEnum;
use App\Common\Services\BaseService;
use App\Models\ProductModel;
use App\Sdks\Yw\YwSdk;

class SyncProductService extends BaseService
{


    public function kyy($item){

        $productModel = new ProductModel();

        $sdk = new YwSdk('',$item->account,$item->cp_secret);
        $data = $sdk->getKyyProduct([
            'start_time'  => strtotime('2020-06-01'),
            'end_time'    => time()
        ]);

        foreach ($data['list'] as $product){
            $pro = $productModel->where('cp_product_alias',$product['appflag'])
                ->where('type',ProductTypeEnums::KYY)
                ->where('cp_account_id',$item->id)
                ->first();

            if(empty($pro)){
                $pro = new ProductModel();
                $pro->cp_account_id = $item->id;
                $pro->cp_product_alias = $product['appflag'];
                $pro->cp_type = CpTypeEnums::YW;
                $pro->type = ProductTypeEnums::KYY;
                $pro->secret = md5(uniqid());
                $pro->status = StatusEnum::DISABLE;
            }

            $pro->name = $product['app_name'];
            $pro->save();
        }
    }



    public function h5($item){

        $productModel = new ProductModel();

        $sdk = new YwSdk('',$item->account,$item->cp_secret);
        $data = $sdk->getH5Product([
            'start_time'  => strtotime('2019-06-01'),
            'end_time'    => time()
        ]);

        foreach ($data['list'] as $product){
            $pro = $productModel
                ->where('cp_account_id',$item->id)
                ->where('cp_product_alias',$product['appflag'])
                ->where('type',ProductTypeEnums::H5)
                ->first();
            if(empty($pro)){
                $pro = new ProductModel();
                $pro->cp_account_id = $item->id;
                $pro->cp_product_alias = $product['appflag'];
                $pro->cp_type = CpTypeEnums::YW;
                $pro->type = ProductTypeEnums::H5;
                $pro->secret = md5(uniqid());
                $pro->status = StatusEnum::DISABLE;
            }

            $pro->name = $product['app_name'];
            $pro->save();
        }
    }

}
