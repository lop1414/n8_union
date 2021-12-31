<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Helpers\Functions;
use App\Services\Cp\CpProviderService;

class PullCpChannelCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'pull_cp_channel {--cp_type=} {--date=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '同步渠道';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }


    public function handle(){

        $cpTypeParam = $this->option('cp_type');
        if(!empty($cpTypeParam)){
            Functions::hasEnum(CpTypeEnums::class,$cpTypeParam);
        }

        list($startDate,$endDate) = Functions::getDateRange($this->option('date'));


        $expire = env('APP_DEBUG') ? 1 : 60 * 60;

        $this->lockRun(function () use ($cpTypeParam,$startDate,$endDate){
            $services = CpProviderService::getCpChannelServices();
            foreach ($services as $service){
                $cpType = $service->getCpType();
                if(empty($cpTypeParam) || $cpTypeParam == $cpType){
                    echo "{$cpType}\n";
                    $service->setParam('start_date',$startDate);
                    $service->setParam('end_date',$endDate);
                    // 阅文只同步快应用
                    if($cpType == CpTypeEnums::YW){
                        $service->setParam('product_type',ProductTypeEnums::KYY);
                    }
                    $service->syncWithHook();
                }
            }
        },'pull_channel',$expire,['log' => true]);

    }
}
