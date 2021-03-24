<?php

namespace App\Console\Commands\Bm;

use App\Common\Console\BaseCommand;
use App\Common\Helpers\Functions;
use App\Services\Bm\CpChannelService;

class PullCpChannelCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'bm:pull_cp_channel {--date=}';

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

        list($startDate,$endDate) = Functions::getDateRange($this->option('date'));
        (new CpChannelService())->sync($startDate,$endDate);

    }
}
