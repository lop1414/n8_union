<?php

namespace App\Console\Commands\Qywx;

use App\Common\Console\BaseCommand;
use App\Services\Qywx\QywxService;

class QywxRefreshAccessTokenCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'qywx:refresh_access_token {--key_suffix=}';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '企业微信刷新access_token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * @throws \App\Common\Tools\CustomException
     * 处理
     */
    public function handle(){
        $param = $this->option();

        // 锁 key
        $lockKey = 'qywx_refresh_access_token';

        // key 后缀
        if(!empty($param['key_suffix'])){
            $lockKey .= '_'. trim($param['key_suffix']);
        }

        $QywxService = new QywxService();
        $option = ['log' => true];
        $this->lockRun(
            [$QywxService, 'refreshAccessToken'],
            $lockKey,
            43200,
            $option,
            $param
        );
    }
}
