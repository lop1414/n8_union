<?php

namespace App\Console\Commands\Check;

use App\Common\Console\BaseCommand;
use App\Services\Check\ChannelClaimService;

class ChannelClaimCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'check:channel_claim';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '检测渠道认领';

    protected $consoleEchoService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }



    public function handle(){

        $this->lockRun(function (){
            (new ChannelClaimService())->index();
        },'check:channel_claim', 60*60,['log' => true]);
    }


}
