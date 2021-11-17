<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Enums\CpTypeEnums;
use App\Common\Enums\ProductTypeEnums;
use App\Common\Helpers\Functions;

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
            $services = $this->getServices();
            foreach ($services as $cpType => $service){
                if(empty($cpTypeParam) || $cpTypeParam == $cpType){
                    echo "{$service['name']}\n";
                    $service = new $service['class'];
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


    public function getServices(){
        return [
            CpTypeEnums::BM => [
                'name' => '笔墨',
                'class' => \App\Services\Cp\Channel\BmChannelService::class
            ],
            CpTypeEnums::TW =>[
                'name' => '腾文',
                'class' => \App\Services\Cp\Channel\TwChannelService::class
            ],
            CpTypeEnums::QY =>[
                'name' => '七悦',
                'class' => \App\Services\Cp\Channel\QyChannelService::class
            ],
            CpTypeEnums::YW =>[
                'name' => '阅文',
                'class' => \App\Services\Cp\Channel\YwChannelService::class
            ],
        ];
    }
}
