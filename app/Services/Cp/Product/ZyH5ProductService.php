<?php

namespace App\Services\Cp\Product;

use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Sdks\ZyH5\ZyH5Sdk;

class ZyH5ProductService implements CpProductInterface
{


    public function get($cpAccount): array
    {

        $sdk = new ZyH5Sdk($cpAccount['account'],$cpAccount['cp_secret']);
        $list =  $sdk->getProduct();

        $data = [];
        foreach ($list['data'] as $item){
            $data[] = [
                'cp_account_id'     => $cpAccount['id'],
                'cp_product_alias'  => $item['id'],
                'cp_type'           => $this->getCpType(),
                'type'              => $this->getType(),
                'name'              => $item['name'],
                'cp_secret'         => $item['api_key']
            ];
        }
        return $data;
    }

    public function getCpType(): string
    {
        return CpTypeEnums::ZY;
    }

    public function getType(): string
    {
        return ProductTypeEnums::H5;
    }
}
