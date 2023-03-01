<?php

namespace App\Services\Cp\Product;

use App\Common\Enums\ProductTypeEnums;

class FqH5ProductService extends FqKyyProductService
{

    protected $appType = 3;


    public function getType(): string
    {
        return ProductTypeEnums::H5;
    }
}
