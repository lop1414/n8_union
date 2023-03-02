<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\ProductTypeEnums;
use App\Services\Cp\Product\FqKyyProductService;


class FqH5ChannelService extends FqKyyProductService
{



    public function getType(): string
    {
        return ProductTypeEnums::H5;
    }

}
