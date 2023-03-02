<?php

namespace App\Services\Cp\Channel;


use App\Common\Enums\ProductTypeEnums;


class FqH5ChannelService extends FqKyyChannelService
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getType(): string
    {
        return ProductTypeEnums::H5;
    }

}
