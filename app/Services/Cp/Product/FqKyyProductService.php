<?php

namespace App\Services\Cp\Product;

use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Sdks\Fq\FqSdk;
use App\Datas\CpAccountData;

class FqKyyProductService implements CpProductInterface
{

    protected $appType = 1;

    public function get($cpAccount): array
    {
        $sdk = new FqSdk($cpAccount['account'],$cpAccount['cp_secret']);
        $data = [];
        $page = $total = 0;
        $cpAccountModelData = new CpAccountData();
        do{
            $list = $sdk->getProducts($this->appType,$page);
            dump($list);
            foreach ($list['package_info_open_list'] as $item){
                $total += 1;
                $newAccount = $cpAccountModelData->save([
                    'account'       => $item['distributor_id'],
                    'cp_secret'     => $cpAccount['cp_secret'],
                    'cp_type'       => $this->getCpType(),
                ]);
                $data[] = [
                    'cp_account_id'     => $newAccount['id'],
                    'cp_product_alias'  => $item['app_id'],
                    'cp_type'           => $this->getCpType(),
                    'type'              => $this->getType(),
                    'name'              => $item['app_name']
                ];
            }
            $page += 1;
        }while($total < $list['total']);

        return $data;
    }

    public function getCpType(): string
    {
        return CpTypeEnums::FQ;
    }

    public function getType(): string
    {
        return ProductTypeEnums::KYY;
    }
}
