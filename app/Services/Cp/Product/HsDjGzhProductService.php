<?php

namespace App\Services\Cp\Product;

use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Sdks\Hs\HsSdk;


class HsDjGzhProductService implements CpProductInterface
{


    public function get($cpAccount): array
    {
        list($apiKey,$apiSecurity) = explode('#',$cpAccount['cp_secret']);
        $sdk = new HsSdk($cpAccount['account'],$apiKey,$apiSecurity);

        $data = [];
        $appletList = $sdk->getApplet();
        foreach ($appletList as $applet){
            $optimizerList = $sdk->getOptimizer($applet['applet_id']);
            foreach ($optimizerList as $optimizer){
                $productList = $sdk->getProduct($optimizer['show_id']);
                foreach ($productList as $product){
                    $data[] = [
                        'cp_account_id'     => $cpAccount['id'],
                        'cp_product_alias'  => $product['id'],
                        'cp_type'           => $this->getCpType(),
                        'type'              => $this->getType(),
                        'name'              => $product['name'],
                        'extends'           => [
                            'applet_id'  => $applet['applet_id'],
                            'show_id'    => $optimizer['show_id']
                        ]
                    ];
                }
            }
        }
        return $data;
    }

    public function getCpType(): string
    {
        return CpTypeEnums::HS;
    }

    public function getType(): string
    {
        return ProductTypeEnums::DJ_GZH;
    }
}
