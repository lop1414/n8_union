<?php

namespace App\Services\Cp;

use App\Models\CpAdminAccountModel;
use App\Services\AdminUserService;
use App\Services\Cp\AdminAccount\CpAdminAccountInterface;
use App\Services\Cp\AdminAccount\FqAdminAccountService;
use App\Services\ProductService;

class CpAdminAccountService
{

    private $param;

    private $service;


    public function __construct(CpAdminAccountInterface $service)
    {
        $this->service = $service;
    }


    /**
     * @return string[]
     * 获取书城渠道服务列表
     */
    static public function getServices(): array
    {
        return [
            FqAdminAccountService::class,
        ];
    }

    public function __call($name, $arguments)
    {
        return $this->service->$name(...$arguments);
    }

    public function sync()
    {
        $productIds =  $this->getParam('product_ids');
        $where = $productIds
            ? ['product_ids' => $this->getParam('product_ids')]
            : ['cp_type'   => $this->service->getCpType()];

        $productList = ProductService::get($where);

        $adminIdMap = array_column((new AdminUserService())->getAdminUserMap(),'id','name');

        foreach ($productList as $product){
            $items = $this->service->get($product);
            foreach ($items as $item){
                if(!isset($adminIdMap[$item['optimizer_nickname']])){
                    echo "{$this->service->getCpType()}:管理员 {$item['optimizer_nickname']} 不存在\n";
                    continue;
                }
                $adminId = $adminIdMap[$item['optimizer_nickname']];
                $cpAdminAccount = (new CpAdminAccountModel())
                    ->where('admin_id',$adminId)
                    ->where('cp_type',$product['cp_type'])
                    ->first();

                if(!$cpAdminAccount){
                    $cpAdminAccount = new CpAdminAccountModel();
                    $cpAdminAccount->admin_id = $adminId;
                }
                $cpAdminAccount->cp_type = $product['cp_type'];
                $cpAdminAccount->extends = $item;
                $cpAdminAccount->save();
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
