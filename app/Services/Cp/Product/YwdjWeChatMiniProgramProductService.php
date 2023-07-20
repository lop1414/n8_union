<?php

namespace App\Services\Cp\Product;

use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Sdks\YwFx\YwFxSdk;

class YwdjWeChatMiniProgramProductService implements CpProductInterface
{


    public function get($cpAccount): array
    {
        $sdk = new YwFxSdk('',$cpAccount['account'],$cpAccount['cp_secret']);

        $list = $sdk->getProduct('2023-06-14',date('Y-m-d H:i:s',time()));

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
        return CpTypeEnums::YWDJ;
    }

    public function getType(): string
    {
        return ProductTypeEnums::WECHAT_MINI_PROGRAM;
    }
}
