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

        $sdk = new YwSdk('',$item->account,$item->cp_secret);
        $data = $sdk->getKyyProduct([
            'start_time'  => strtotime('2020-06-01'),
            'end_time'    => time()
        ]);

        $ret = [];
        foreach ($data['list'] as $product){
            $ret[] = [
                'cp_account_id'     => $item->id,
                'cp_product_alias'  => $product['appflag'],
                'cp_type'           => CpTypeEnums::YW,
                'type'              => ProductTypeEnums::KYY,
                'name'              => $product['app_name']
            ];
        }
        return $ret;
    }



    public function h5($item){


        $sdk = new YwSdk('',$item->account,$item->cp_secret);
        $data = $sdk->getH5Product([
            'start_time'  => strtotime('2019-06-01'),
            'end_time'    => time()
        ]);

        $ret = [];
        foreach ($data['list'] as $product){
            $ret[] = [
                'cp_account_id'     => $item->id,
                'cp_product_alias'  => $product['appflag'],
                'cp_type'           => CpTypeEnums::YW,
                'type'              => ProductTypeEnums::H5,
                'name'              => $product['app_name']
            ];
        }
        return $ret;
    }

}
