<?php

namespace App\Console\Commands;

use App\Common\Console\BaseCommand;
use App\Models\WeixinMiniProgramModel;
use App\Services\Weixin\MiniProgram\WeixinMiniProgramAuthService;

class RefreshWeixinMiniProgramAccessTokenCommand extends BaseCommand
{
    /**
     * 命令行执行命令
     * @var string
     */
    protected $signature = 'refresh_weixin_program_access_token';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '刷新小程序token';



    public function handle(){


        $key = "refresh_weixin_program_access_token";

        $this->lockRun(function (){
            $list = (new WeixinMiniProgramModel())->get();

            $service = new WeixinMiniProgramAuthService();

            foreach ($list as $item){
                $service->setApp($item->app_id);
                $service->refreshAccessToken();
            }
        },$key, 60*60*3,['log' => true]);
    }


}
