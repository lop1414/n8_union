<?php

namespace App\Services\Cp\Account;

interface CpAccountInterface
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
     * @param $cpAccount
     * @return string
     * 获取账户token
     */
    public function getToken($cpAccount): string;


}
