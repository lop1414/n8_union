<?php

namespace App\Services\Cp\Account;

use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Sdks\Mb\MbSdk;

class MbDyMiniProgramAccountService implements CpAccountInterface
{


    public function getToken($cpAccount): string
    {
        $sdk = new MbSdk($cpAccount['account'],'','');
        return $sdk->getToken();
    }


    public function getCpType(): string
    {
        return CpTypeEnums::MB;
    }

    public function getType(): string
    {
        return ProductTypeEnums::DY_MINI_PROGRAM;
    }
}
