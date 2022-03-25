<?php

namespace App\Services\Cp\Product;

use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Sdks\Yw\YwSdk;

class YwKyyProductService implements CpProductInterface
{


    public function get($cpAccount): array
    {
        $sdk = new YwSdk('',$cpAccount['account'],$cpAccount['cp_secret']);

        $list = $sdk->getKyyProduct([
            'start_time'  => strtotime('2020-06-01'),
            'end_time'    => time()
        ]);

        $data = [];
        foreach ($list['list'] as $item){
            $data[] = [
                'cp_account_id'     => $cpAccount['id'],
                'cp_product_alias'  => $item['appflag'],
                'cp_type'           => $this->getCpType(),
                'type'              => $this->getType(),
                'name'              => $item['app_name']
            ];
        }
        return $data;
    }

    public function getCpType(): string
    {
        return CpTypeEnums::YW;
    }

    public function getType(): string
    {
        return ProductTypeEnums::KYY;
    }
}
