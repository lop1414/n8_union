<?php

namespace App\Services\Cp\Book;



use App\Models\ProductModel;

interface CpBookInterface
{

    /**
     * @return string
     * 获取平台类型
     */
    public function getCpType(): string;

    public function read(ProductModel $product,string $cpId): array;


}
