<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Enums\CpTypeEnums;
use App\Common\Helpers\Functions;
use App\Services\Cp\AdminAccount\CpAdminAccountInterface;
use App\Services\Cp\CpAdminAccountService;
use Illuminate\Container\Container;

class SyncCpAdminAccountCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'sync_cp_admin_account  {--cp_type=} {--product_id=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '同步书城管理员账户信息';



    public function handle(){

        $this->lockRun(function (){
            $cpType = $this->option('cp_type');
            if(!empty($cpType)){
                Functions::hasEnum(CpTypeEnums::class,$cpType);
            }

            $productId = $this->option('product_id') ?: 0;

            $this->sync($cpType,$productId);

        },'sync_cp_admin_account',60 * 60,['log' => true]);
    }


    public function sync($cpType, $productId)
    {

        if(!empty($cpType)){
            Functions::hasEnum(CpTypeEnums::class,$cpType);
        }

        $container = Container::getInstance();
        $services = CpAdminAccountService::getServices();


        foreach ($services as $service){

            $container->bind(CpAdminAccountInterface::class,$service);
            $cpAdminAccountService = $container->make(CpAdminAccountService::class);

            if(!empty($productType) && $productType != $cpAdminAccountService->getType()){
                continue;
            }

            if(!empty($cpType) && $cpType != $cpAdminAccountService->getCpType()){
                continue;
            }


            if(!empty($productId)){
                $cpAdminAccountService->setParam('product_ids',[$productId]);
            }

            $cpAdminAccountService->sync();
        }

    }


}
