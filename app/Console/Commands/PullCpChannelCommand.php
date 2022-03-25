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

            $param = [];

            $cpTypeParam = $this->option('cp_type');
            if(!empty($cpTypeParam)){
                Functions::hasEnum(CpTypeEnums::class,$cpTypeParam);
                $param['cp_type'] = $cpTypeParam;
            }

            list($param['start_date'],$param['end_date']) = Functions::getDateRange($this->option('date'));

            (new ChannelService())->sync($param);

        },'pull_channel',$expire,['log' => true]);

    }
}
