<?php

namespace App\Console\Commands\Bm;

use App\Common\Console\BaseCommand;
use App\Common\Helpers\Functions;
use App\Services\Bm\ChannelService;

class PullChannelCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'bm:pull_channel {--date=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '笔墨渠道';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * 处理
     */
    public function handle(){

        $date = $this->option('date');
        if(!empty($date)){
            list($startDate,$endDate) = Functions::getDateRange($this->option('date'));

        }else{
            $startDate = $endDate = null;
        }
        $expire = env('APP_DEBUG') ? 1 : 60 * 60;

        $this->lockRun(function () use ($startDate,$endDate){
            (new ChannelService())->sync($startDate,$endDate);
        },'bm_pull_channel',$expire,['log' => true]);


    }
}
