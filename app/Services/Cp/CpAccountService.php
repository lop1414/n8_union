<?php

namespace App\Services\Cp;


use App\Models\CpAccountModel;
use App\Services\Cp\Account\CpAccountInterface;
use App\Services\Cp\Account\MbAccountService;

class CpAccountService
{

    private $param;

    private $service;



    public function __construct(CpAccountInterface $service)
    {
        $this->service = $service;
    }


    /**
     * @return string[]
     * 获取书城账户服务列表
     */
    static public function getServices(): array
    {
        return [
            MbAccountService::class,
        ];
    }

    public function __call($name, $arguments)
    {
        return $this->service->$name(...$arguments);
    }

    public function refreshToken()
    {
        $cpType = $this->getParam('cp_type');

        $cpAccounts = (new CpAccountModel())
            ->when($cpType,function ($query,$cpType){
                return $query->where('cp_type',$cpType);
            })
            ->get();

        foreach ($cpAccounts as $cpAccount){
            $token = $this->service->getToken($cpAccount);

            if(!empty($token)){
                $cpAccount->cp_secret = $token;
                $cpAccount->save();
            }
        }
    }

    public function getParam($key)
    {
        if(empty($this->param[$key])){
            return null;
        }
        return $this->param[$key];
    }

    public function setParam($key,$data)
    {
        $this->param[$key] = $data;
    }

}
