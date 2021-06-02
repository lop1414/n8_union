<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Common\Tools\CustomException;
use App\Services\Activity\LotteryPrizeLogService;
use App\Services\Activity\LotteryService;
use App\Services\OpenUserService;
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

    /**
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * 详情
     */
    public function read(Request $request){
        $this->validRule($request->post(), [
            'id' => 'required',
        ]);

        $data = $request->post();

        $lotteryService = new LotteryService();
        $lottery = $lotteryService->getCache($data['id']);
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

        // 获取用户信息
        $openUserService = new OpenUserService();
        $info = $openUserService->info($data);

        // 剩余抽奖次数
        $lotteryPrizeLogService = new LotteryPrizeLogService();
        $lotteryTimes = $lotteryPrizeLogService->getLotteryTimes($info['n8_guid'], $lottery['id'], $lottery['cycle_type']);
        $lottery['over_lottery_times'] = max($lottery['max_times'] - $lotteryTimes, 0);

        return $this->success($lottery);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * 抽奖
     */
    public function draw(Request $request){
        $requestData = $request->post();

        // 抽奖
        $lotteryService = new LotteryService();
        $ret = $lotteryService->draw($requestData);

        // 获取奖品
        $prize = $lotteryService->getPrize();
        unset($prize['chance'], $prize['total']);

        return $this->ret($ret, $prize);
    }
}
