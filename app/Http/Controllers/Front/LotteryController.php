<?php

namespace App\Http\Controllers\Front;

use App\Common\Controllers\Front\FrontController;
use App\Common\Tools\CustomException;
use App\Services\LotteryService;
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
     * @throws CustomException
     * 详情
     */
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

    /**
     * @param Request $request
     * @return mixed
     * @throws CustomException
     * 抽奖
     */
    public function draw(Request $request){
        $requestData = $request->post();

        $lotteryService = new LotteryService();
        $ret = $lotteryService->draw($requestData);

        return $this->ret($ret);
    }
}
