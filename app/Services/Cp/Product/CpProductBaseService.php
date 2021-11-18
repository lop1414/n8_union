<?php

namespace App\Services\Cp\Product;

use App\Datas\ProductData;
use App\Services\Cp\CpBaseService;

class CpProductBaseService extends CpBaseService
{

    public function save($data){
        (new ProductData())->save([
            'cp_account_id' => $data['cp_account_id'],
            'cp_product_alias' => $data['cp_product_alias'],
            'cp_type'       => $data['cp_type'],
            'type'          => $data['type'],
            'name'          => $data['name'],
        ]);
    }
}
