<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Common\Enums\CpTypeEnums;
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


    protected $startDate,$endDate;

    protected $cpType;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }


    public function handle(){

        $date = $this->option('date');
        $this->cpType = $this->option('cp_type');
        if(!empty($this->cpType)){
            Functions::hasEnum(CpTypeEnums::class,$this->cpType);
        }

        if(!empty($date)){
            list($this->startDate,$this->endDate) = Functions::getDateRange($this->option('date'));

        }

        $expire = env('APP_DEBUG') ? 1 : 60 * 60;

        $this->lockRun(function (){
            $this->sync();
        },'pull_channel',$expire,['log' => true]);

    }



    public function sync(){
        if(empty($this->cpType) || $this->cpType == CpTypeEnums::YW){
            echo "阅文\n";
            (new \App\Services\Yw\ChannelService())->sync($this->startDate,$this->endDate);
        }

        if(empty($this->cpType) || $this->cpType == CpTypeEnums::BM){
            echo "笔墨\n";
            (new \App\Services\Bm\ChannelService())->sync($this->startDate,$this->endDate);
        }

        if(empty($this->cpType) || $this->cpType == CpTypeEnums::TW){
            echo "腾文\n";
            (new \App\Services\Tw\ChannelService())->sync($this->startDate,$this->endDate);
        }

        if(empty($this->cpType) || $this->cpType == CpTypeEnums::QY){
            echo "七悦\n";
            (new \App\Services\Qy\ChannelService())->sync($this->startDate,$this->endDate);
        }

    }
}
