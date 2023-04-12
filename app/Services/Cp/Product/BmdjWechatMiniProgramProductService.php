<?php

namespace App\Services\Cp\Product;

use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Sdks\Bmdj\BmdjSdk;

class BmdjWechatMiniProgramProductService implements CpProductInterface
{


    public function get($cpAccount): array
    {
        $sdk = new BmdjSdk($cpAccount['account'],$cpAccount['cp_secret']);
        $data = [];
        $page = 1;
        do{
            $list = $sdk->getProducts($page);

            foreach ($list['list'] as $item){

                $data[] = [
                    'cp_account_id'     => $cpAccount['id'],
                    'cp_product_alias'  => $item['agentid'],
                    'cp_type'           => $this->getCpType(),
                    'type'              => $this->getType(),
                    'name'              => $item['name']
                ];
            }
            $page += 1;
        }while($list['page'] < $list['totalPage']);

        return $data;
    }

    public function getCpType(): string
    {
        return CpTypeEnums::BMDJ;
    }

    public function getType(): string
    {
        return ProductTypeEnums::WECHAT_MINI_PROGRAM;
    }
}
