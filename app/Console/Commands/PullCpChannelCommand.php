<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Enums\CpTypeEnums;
use App\Common\Helpers\Functions;
use App\Services\ChannelService;

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

        $expire = env('APP_DEBUG') ? 1 : 60 * 60;

        $this->lockRun(function (){

            $cpType = $this->option('cp_type');
            if(!empty($cpType)){
                Functions::hasEnum(CpTypeEnums::class,$cpType);
            }

            list($startDate,$endDate) = Functions::getDateRange($this->option('date'));

            (new ChannelService())->sync($cpType, '', $startDate, $endDate);

        },'pull_channel',$expire,['log' => true]);

    }
}
