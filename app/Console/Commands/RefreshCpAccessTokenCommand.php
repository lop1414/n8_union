<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Enums\CpTypeEnums;
use App\Common\Helpers\Functions;
use App\Services\Cp\CpAccountService;
use App\Services\Cp\Account\CpAccountInterface;
use Illuminate\Container\Container;

class RefreshCpAccessTokenCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'refresh_cp_access_token  {--cp_type=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '刷新平台账户token';



    public function handle(){


        $key = "refresh_mb_access_token";

        $this->lockRun(function (){

            $cpTypeParam = $this->option('cp_type');
            if(!empty($cpTypeParam)){
                Functions::hasEnum(CpTypeEnums::class,$cpTypeParam);
            }

            $container = Container::getInstance();

            $services = CpAccountService::getServices();

            foreach ($services as $service){

                $container->bind(CpAccountInterface::class,$service);
                $cpAccountService = $container->make(CpAccountService::class);

                $cpType = $cpAccountService->getCpType();

                if(empty($cpTypeParam) || $cpTypeParam == $cpType){
                    $cpAccountService->setParam('cp_type',$cpType);
                    $cpAccountService->refreshToken();
                }
            }
        },$key, 60*60*3,['log' => true]);
    }


}
