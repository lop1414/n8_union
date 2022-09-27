<?php

namespace App\Services\Cp\Chapter;



use App\Models\ProductModel;

interface CpChapterInterface
{

    /**
     * @return string
     * 获取平台类型
     */
    public function getCpType(): string;

    public function get(ProductModel $product,int $bookId): array;


}
