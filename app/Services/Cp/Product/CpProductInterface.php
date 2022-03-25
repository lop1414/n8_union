<?php

namespace App\Services\Cp\Product;

use App\Models\CpAccountModel;

interface CpProductInterface
{
    /**
     * @return string
     * 获取平台类型
     */
    public function getCpType(): string;

    /**
     * @return string
     * 获取产品类型
     */
    public function getType(): string;


    /**
     * @param CpAccountModel $cpAccount
     * @return array
     */
    public function get(CpAccountModel $cpAccount): array;


}
