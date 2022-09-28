<?php

namespace App\Services\Cp\AdminAccount;

use App\Common\Enums\CpTypeEnums;
use App\Common\Sdks\Fq\FqSdk;
use App\Models\ProductModel;

class FqAdminAccountService implements CpAdminAccountInterface
{

    public function getCpType(): string
    {
        return CpTypeEnums::MB;
    }



    public function get(ProductModel $product):array
    {
        $sdk = new FqSdk($product['cp_account']['account'],$product['cp_account']['cp_secret']);


        $res = $sdk->getOptimizerList();

        return $res['result'] ?? [];
    }

}
