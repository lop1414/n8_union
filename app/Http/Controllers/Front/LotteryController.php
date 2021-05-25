<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Common\Tools\CustomException;
use App\Services\LotteryService;
use App\Services\Weixin\WeixinMiniProgramAuthService;
use App\Services\Weixin\WeixinMiniProgramService;
use Illuminate\Http\Request;

class LotteryController extends FrontController
{
    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function read(Request $request){
        $this->validRule($request->post(), [
            'id' => 'required',
        ]);

        $id = $request->post('id');

        $lotteryService = new LotteryService();
        $lottery = $lotteryService->getCache($id);
        if(empty($lottery)){
            throw new CustomException([
                'code' => 'NOT_FOUND_LOTTERY',
                'message' => '找不到该抽奖活动',
            ]);
        }

        unset($lottery->release_data, $lottery->release_at);
        foreach($lottery['lottery_prizes'] as $k => $v){
            unset($lottery['lottery_prizes'][$k]['chance']);
            unset($lottery['lottery_prizes'][$k]['total']);
        }
        dd($lottery);
    }

    public function draw(Request $request){
        $this->validRule($request->post(), [
            'id' => 'required',
        ]);

        $id = $request->post('id');
        $guid = $request->post('guid');

        if(empty($guid)){
            $jsCode = $request->post('js_code');
            $appId = $request->post('app_id');

            $weixinMiniProgramAuthService = new WeixinMiniProgramAuthService();
            $weixinMiniProgramAuthService->setApp($appId);
            $openId = $weixinMiniProgramAuthService->getOpenIdByJsCode($jsCode);
            $openId = "oZLu95TzGQPy6aP4KNiIBEUz_bHo";


            dd($openId);
        }

        if(empty($guid)){
            throw new CustomException([
                'code' => 'NOT_FOUND_USER',
                'message' => '找不到用户信息',
            ]);
        }

        dd($guid);

        $lotteryService = new LotteryService();
        $ret = $lotteryService->draw($id);

        return $this->ret($ret);
    }
}
